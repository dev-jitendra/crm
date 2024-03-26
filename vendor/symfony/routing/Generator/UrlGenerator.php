<?php



namespace Symfony\Component\Routing\Generator;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;


class UrlGenerator implements UrlGeneratorInterface, ConfigurableRequirementsInterface
{
    private const QUERY_FRAGMENT_DECODED = [
        
        '%2F' => '/',
        '%3F' => '?',
        
        
        '%40' => '@',
        '%3A' => ':',
        '%21' => '!',
        '%3B' => ';',
        '%2C' => ',',
        '%2A' => '*',
    ];

    protected $routes;
    protected $context;

    
    protected $strictRequirements = true;

    protected $logger;

    private ?string $defaultLocale;

    
    protected $decodedChars = [
        
        
        
        '%2F' => '/',
        '%252F' => '%2F',
        
        
        '%40' => '@',
        '%3A' => ':',
        
        
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
    ];

    public function __construct(RouteCollection $routes, RequestContext $context, LoggerInterface $logger = null, string $defaultLocale = null)
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->logger = $logger;
        $this->defaultLocale = $defaultLocale;
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    
    public function setStrictRequirements(?bool $enabled)
    {
        $this->strictRequirements = $enabled;
    }

    
    public function isStrictRequirements(): ?bool
    {
        return $this->strictRequirements;
    }

    
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $route = null;
        $locale = $parameters['_locale']
            ?? $this->context->getParameter('_locale')
            ?: $this->defaultLocale;

        if (null !== $locale) {
            do {
                if (null !== ($route = $this->routes->get($name.'.'.$locale)) && $route->getDefault('_canonical_route') === $name) {
                    break;
                }
            } while (false !== $locale = strstr($locale, '_', true));
        }

        if (null === $route = $route ?? $this->routes->get($name)) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        
        $compiledRoute = $route->compile();

        $defaults = $route->getDefaults();
        $variables = $compiledRoute->getVariables();

        if (isset($defaults['_canonical_route']) && isset($defaults['_locale'])) {
            if (!\in_array('_locale', $variables, true)) {
                unset($parameters['_locale']);
            } elseif (!isset($parameters['_locale'])) {
                $parameters['_locale'] = $defaults['_locale'];
            }
        }

        return $this->doGenerate($variables, $defaults, $route->getRequirements(), $compiledRoute->getTokens(), $parameters, $name, $referenceType, $compiledRoute->getHostTokens(), $route->getSchemes());
    }

    
    protected function doGenerate(array $variables, array $defaults, array $requirements, array $tokens, array $parameters, string $name, int $referenceType, array $hostTokens, array $requiredSchemes = []): string
    {
        $variables = array_flip($variables);
        $mergedParams = array_replace($defaults, $this->context->getParameters(), $parameters);

        
        if ($diff = array_diff_key($variables, $mergedParams)) {
            throw new MissingMandatoryParametersException(sprintf('Some mandatory parameters are missing ("%s") to generate a URL for route "%s".', implode('", "', array_keys($diff)), $name));
        }

        $url = '';
        $optional = true;
        $message = 'Parameter "{parameter}" for route "{route}" must match "{expected}" ("{given}" given) to generate a corresponding URL.';
        foreach ($tokens as $token) {
            if ('variable' === $token[0]) {
                $varName = $token[3];
                
                $important = $token[5] ?? false;

                if (!$optional || $important || !\array_key_exists($varName, $defaults) || (null !== $mergedParams[$varName] && (string) $mergedParams[$varName] !== (string) $defaults[$varName])) {
                    
                    if (null !== $this->strictRequirements && !preg_match('#^'.preg_replace('/\(\?(?:=|<=|!|<!)((?:[^()\\\\]+|\\\\.|\((?1)\))*)\)/', '', $token[2]).'$#i'.(empty($token[4]) ? '' : 'u'), $mergedParams[$token[3]] ?? '')) {
                        if ($this->strictRequirements) {
                            throw new InvalidParameterException(strtr($message, ['{parameter}' => $varName, '{route}' => $name, '{expected}' => $token[2], '{given}' => $mergedParams[$varName]]));
                        }

                        if ($this->logger) {
                            $this->logger->error($message, ['parameter' => $varName, 'route' => $name, 'expected' => $token[2], 'given' => $mergedParams[$varName]]);
                        }

                        return '';
                    }

                    $url = $token[1].$mergedParams[$varName].$url;
                    $optional = false;
                }
            } else {
                
                $url = $token[1].$url;
                $optional = false;
            }
        }

        if ('' === $url) {
            $url = '/';
        }

        
        $url = strtr(rawurlencode($url), $this->decodedChars);

        
        
        
        $url = strtr($url, ['/../' => '/%2E%2E/', '/./' => '/%2E/']);
        if (str_ends_with($url, '/..')) {
            $url = substr($url, 0, -2).'%2E%2E';
        } elseif (str_ends_with($url, '/.')) {
            $url = substr($url, 0, -1).'%2E';
        }

        $schemeAuthority = '';
        $host = $this->context->getHost();
        $scheme = $this->context->getScheme();

        if ($requiredSchemes) {
            if (!\in_array($scheme, $requiredSchemes, true)) {
                $referenceType = self::ABSOLUTE_URL;
                $scheme = current($requiredSchemes);
            }
        }

        if ($hostTokens) {
            $routeHost = '';
            foreach ($hostTokens as $token) {
                if ('variable' === $token[0]) {
                    
                    if (null !== $this->strictRequirements && !preg_match('#^'.preg_replace('/\(\?(?:=|<=|!|<!)((?:[^()\\\\]+|\\\\.|\((?1)\))*)\)/', '', $token[2]).'$#i'.(empty($token[4]) ? '' : 'u'), $mergedParams[$token[3]])) {
                        if ($this->strictRequirements) {
                            throw new InvalidParameterException(strtr($message, ['{parameter}' => $token[3], '{route}' => $name, '{expected}' => $token[2], '{given}' => $mergedParams[$token[3]]]));
                        }

                        if ($this->logger) {
                            $this->logger->error($message, ['parameter' => $token[3], 'route' => $name, 'expected' => $token[2], 'given' => $mergedParams[$token[3]]]);
                        }

                        return '';
                    }

                    $routeHost = $token[1].$mergedParams[$token[3]].$routeHost;
                } else {
                    $routeHost = $token[1].$routeHost;
                }
            }

            if ($routeHost !== $host) {
                $host = $routeHost;
                if (self::ABSOLUTE_URL !== $referenceType) {
                    $referenceType = self::NETWORK_PATH;
                }
            }
        }

        if (self::ABSOLUTE_URL === $referenceType || self::NETWORK_PATH === $referenceType) {
            if ('' !== $host || ('' !== $scheme && 'http' !== $scheme && 'https' !== $scheme)) {
                $port = '';
                if ('http' === $scheme && 80 !== $this->context->getHttpPort()) {
                    $port = ':'.$this->context->getHttpPort();
                } elseif ('https' === $scheme && 443 !== $this->context->getHttpsPort()) {
                    $port = ':'.$this->context->getHttpsPort();
                }

                $schemeAuthority = self::NETWORK_PATH === $referenceType || '' === $scheme ? '
                $schemeAuthority .= $host.$port;
            }
        }

        if (self::RELATIVE_PATH === $referenceType) {
            $url = self::getRelativePath($this->context->getPathInfo(), $url);
        } else {
            $url = $schemeAuthority.$this->context->getBaseUrl().$url;
        }

        
        $extra = array_udiff_assoc(array_diff_key($parameters, $variables), $defaults, function ($a, $b) {
            return $a == $b ? 0 : 1;
        });

        array_walk_recursive($extra, $caster = static function (&$v) use (&$caster) {
            if (\is_object($v)) {
                if ($vars = get_object_vars($v)) {
                    array_walk_recursive($vars, $caster);
                    $v = $vars;
                } elseif (method_exists($v, '__toString')) {
                    $v = (string) $v;
                }
            }
        });

        
        $fragment = $defaults['_fragment'] ?? '';

        if (isset($extra['_fragment'])) {
            $fragment = $extra['_fragment'];
            unset($extra['_fragment']);
        }

        if ($extra && $query = http_build_query($extra, '', '&', \PHP_QUERY_RFC3986)) {
            $url .= '?'.strtr($query, self::QUERY_FRAGMENT_DECODED);
        }

        if ('' !== $fragment) {
            $url .= '#'.strtr(rawurlencode($fragment), self::QUERY_FRAGMENT_DECODED);
        }

        return $url;
    }

    
    public static function getRelativePath(string $basePath, string $targetPath): string
    {
        if ($basePath === $targetPath) {
            return '';
        }

        $sourceDirs = explode('/', isset($basePath[0]) && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);
        $targetDirs = explode('/', isset($targetPath[0]) && '/' === $targetPath[0] ? substr($targetPath, 1) : $targetPath);
        array_pop($sourceDirs);
        $targetFile = array_pop($targetDirs);

        foreach ($sourceDirs as $i => $dir) {
            if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) {
                unset($sourceDirs[$i], $targetDirs[$i]);
            } else {
                break;
            }
        }

        $targetDirs[] = $targetFile;
        $path = str_repeat('../', \count($sourceDirs)).implode('/', $targetDirs);

        
        
        
        
        return '' === $path || '/' === $path[0]
            || false !== ($colonPos = strpos($path, ':')) && ($colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos)
            ? "./$path" : $path;
    }
}
