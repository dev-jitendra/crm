<?php



namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;


class UrlMatcher implements UrlMatcherInterface, RequestMatcherInterface
{
    public const REQUIREMENT_MATCH = 0;
    public const REQUIREMENT_MISMATCH = 1;
    public const ROUTE_MATCH = 2;

    
    protected $context;

    
    protected $allow = [];

    
    protected array $allowSchemes = [];

    protected $routes;
    protected $request;
    protected $expressionLanguage;

    
    protected $expressionLanguageProviders = [];

    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        $this->routes = $routes;
        $this->context = $context;
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    
    public function match(string $pathinfo): array
    {
        $this->allow = $this->allowSchemes = [];

        if ($ret = $this->matchCollection(rawurldecode($pathinfo) ?: '/', $this->routes)) {
            return $ret;
        }

        if ('/' === $pathinfo && !$this->allow && !$this->allowSchemes) {
            throw new NoConfigurationException();
        }

        throw 0 < \count($this->allow) ? new MethodNotAllowedException(array_unique($this->allow)) : new ResourceNotFoundException(sprintf('No routes found for "%s".', $pathinfo));
    }

    
    public function matchRequest(Request $request): array
    {
        $this->request = $request;

        $ret = $this->match($request->getPathInfo());

        $this->request = null;

        return $ret;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    
    protected function matchCollection(string $pathinfo, RouteCollection $routes): array
    {
        
        if ('HEAD' === $method = $this->context->getMethod()) {
            $method = 'GET';
        }
        $supportsTrailingSlash = 'GET' === $method && $this instanceof RedirectableUrlMatcherInterface;
        $trimmedPathinfo = rtrim($pathinfo, '/') ?: '/';

        foreach ($routes as $name => $route) {
            $compiledRoute = $route->compile();
            $staticPrefix = rtrim($compiledRoute->getStaticPrefix(), '/');
            $requiredMethods = $route->getMethods();

            
            if ('' !== $staticPrefix && !str_starts_with($trimmedPathinfo, $staticPrefix)) {
                continue;
            }
            $regex = $compiledRoute->getRegex();

            $pos = strrpos($regex, '$');
            $hasTrailingSlash = '/' === $regex[$pos - 1];
            $regex = substr_replace($regex, '/?$', $pos - $hasTrailingSlash, 1 + $hasTrailingSlash);

            if (!preg_match($regex, $pathinfo, $matches)) {
                continue;
            }

            $hasTrailingVar = $trimmedPathinfo !== $pathinfo && preg_match('#\{\w+\}/?$#', $route->getPath());

            if ($hasTrailingVar && ($hasTrailingSlash || (null === $m = $matches[\count($compiledRoute->getPathVariables())] ?? null) || '/' !== ($m[-1] ?? '/')) && preg_match($regex, $trimmedPathinfo, $m)) {
                if ($hasTrailingSlash) {
                    $matches = $m;
                } else {
                    $hasTrailingVar = false;
                }
            }

            $hostMatches = [];
            if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $this->context->getHost(), $hostMatches)) {
                continue;
            }

            $status = $this->handleRouteRequirements($pathinfo, $name, $route);

            if (self::REQUIREMENT_MISMATCH === $status[0]) {
                continue;
            }

            if ('/' !== $pathinfo && !$hasTrailingVar && $hasTrailingSlash === ($trimmedPathinfo === $pathinfo)) {
                if ($supportsTrailingSlash && (!$requiredMethods || \in_array('GET', $requiredMethods))) {
                    return $this->allow = $this->allowSchemes = [];
                }
                continue;
            }

            if ($route->getSchemes() && !$route->hasScheme($this->context->getScheme())) {
                $this->allowSchemes = array_merge($this->allowSchemes, $route->getSchemes());
                continue;
            }

            if ($requiredMethods && !\in_array($method, $requiredMethods)) {
                $this->allow = array_merge($this->allow, $requiredMethods);
                continue;
            }

            return $this->getAttributes($route, $name, array_replace($matches, $hostMatches, $status[1] ?? []));
        }

        return [];
    }

    
    protected function getAttributes(Route $route, string $name, array $attributes): array
    {
        $defaults = $route->getDefaults();
        if (isset($defaults['_canonical_route'])) {
            $name = $defaults['_canonical_route'];
            unset($defaults['_canonical_route']);
        }
        $attributes['_route'] = $name;

        return $this->mergeDefaults($attributes, $defaults);
    }

    
    protected function handleRouteRequirements(string $pathinfo, string $name, Route $route): array
    {
        
        if ($route->getCondition() && !$this->getExpressionLanguage()->evaluate($route->getCondition(), ['context' => $this->context, 'request' => $this->request ?: $this->createRequest($pathinfo)])) {
            return [self::REQUIREMENT_MISMATCH, null];
        }

        return [self::REQUIREMENT_MATCH, null];
    }

    
    protected function mergeDefaults(array $params, array $defaults): array
    {
        foreach ($params as $key => $value) {
            if (!\is_int($key) && null !== $value) {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    protected function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new \LogicException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders);
        }

        return $this->expressionLanguage;
    }

    
    protected function createRequest(string $pathinfo): ?Request
    {
        if (!class_exists(Request::class)) {
            return null;
        }

        return Request::create($this->context->getScheme().':
            'SCRIPT_FILENAME' => $this->context->getBaseUrl(),
            'SCRIPT_NAME' => $this->context->getBaseUrl(),
        ]);
    }
}
