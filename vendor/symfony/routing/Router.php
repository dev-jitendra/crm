<?php



namespace Symfony\Component\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Component\Routing\Generator\ConfigurableRequirementsInterface;
use Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
use Symfony\Component\Routing\Generator\Dumper\GeneratorDumperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumperInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;


class Router implements RouterInterface, RequestMatcherInterface
{
    
    protected $matcher;

    
    protected $generator;

    
    protected $context;

    
    protected $loader;

    
    protected $collection;

    
    protected $resource;

    
    protected $options = [];

    
    protected $logger;

    
    protected $defaultLocale;

    private $configCacheFactory;

    
    private array $expressionLanguageProviders = [];

    private static ?array $cache = [];

    public function __construct(LoaderInterface $loader, mixed $resource, array $options = [], RequestContext $context = null, LoggerInterface $logger = null, string $defaultLocale = null)
    {
        $this->loader = $loader;
        $this->resource = $resource;
        $this->logger = $logger;
        $this->context = $context ?? new RequestContext();
        $this->setOptions($options);
        $this->defaultLocale = $defaultLocale;
    }

    
    public function setOptions(array $options)
    {
        $this->options = [
            'cache_dir' => null,
            'debug' => false,
            'generator_class' => CompiledUrlGenerator::class,
            'generator_dumper_class' => CompiledUrlGeneratorDumper::class,
            'matcher_class' => CompiledUrlMatcher::class,
            'matcher_dumper_class' => CompiledUrlMatcherDumper::class,
            'resource_type' => null,
            'strict_requirements' => true,
        ];

        
        $invalid = [];
        foreach ($options as $key => $value) {
            if (\array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('", "', $invalid)));
        }
    }

    
    public function setOption(string $key, mixed $value)
    {
        if (!\array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    
    public function getOption(string $key): mixed
    {
        if (!\array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->loader->load($this->resource, $this->options['resource_type']);
        }

        return $this->collection;
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        if (null !== $this->matcher) {
            $this->getMatcher()->setContext($context);
        }
        if (null !== $this->generator) {
            $this->getGenerator()->setContext($context);
        }
    }

    
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    
    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory)
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->getGenerator()->generate($name, $parameters, $referenceType);
    }

    
    public function match(string $pathinfo): array
    {
        return $this->getMatcher()->match($pathinfo);
    }

    
    public function matchRequest(Request $request): array
    {
        $matcher = $this->getMatcher();
        if (!$matcher instanceof RequestMatcherInterface) {
            
            return $matcher->match($request->getPathInfo());
        }

        return $matcher->matchRequest($request);
    }

    
    public function getMatcher(): UrlMatcherInterface|RequestMatcherInterface
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir']) {
            $routes = $this->getRouteCollection();
            $compiled = is_a($this->options['matcher_class'], CompiledUrlMatcher::class, true);
            if ($compiled) {
                $routes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
            }
            $this->matcher = new $this->options['matcher_class']($routes, $this->context);
            if (method_exists($this->matcher, 'addExpressionLanguageProvider')) {
                foreach ($this->expressionLanguageProviders as $provider) {
                    $this->matcher->addExpressionLanguageProvider($provider);
                }
            }

            return $this->matcher;
        }

        $cache = $this->getConfigCacheFactory()->cache($this->options['cache_dir'].'/url_matching_routes.php',
            function (ConfigCacheInterface $cache) {
                $dumper = $this->getMatcherDumperInstance();
                if (method_exists($dumper, 'addExpressionLanguageProvider')) {
                    foreach ($this->expressionLanguageProviders as $provider) {
                        $dumper->addExpressionLanguageProvider($provider);
                    }
                }

                $cache->write($dumper->dump(), $this->getRouteCollection()->getResources());
            }
        );

        return $this->matcher = new $this->options['matcher_class'](self::getCompiledRoutes($cache->getPath()), $this->context);
    }

    
    public function getGenerator(): UrlGeneratorInterface
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        if (null === $this->options['cache_dir']) {
            $routes = $this->getRouteCollection();
            $compiled = is_a($this->options['generator_class'], CompiledUrlGenerator::class, true);
            if ($compiled) {
                $generatorDumper = new CompiledUrlGeneratorDumper($routes);
                $routes = array_merge($generatorDumper->getCompiledRoutes(), $generatorDumper->getCompiledAliases());
            }
            $this->generator = new $this->options['generator_class']($routes, $this->context, $this->logger, $this->defaultLocale);
        } else {
            $cache = $this->getConfigCacheFactory()->cache($this->options['cache_dir'].'/url_generating_routes.php',
                function (ConfigCacheInterface $cache) {
                    $dumper = $this->getGeneratorDumperInstance();

                    $cache->write($dumper->dump(), $this->getRouteCollection()->getResources());
                }
            );

            $this->generator = new $this->options['generator_class'](self::getCompiledRoutes($cache->getPath()), $this->context, $this->logger, $this->defaultLocale);
        }

        if ($this->generator instanceof ConfigurableRequirementsInterface) {
            $this->generator->setStrictRequirements($this->options['strict_requirements']);
        }

        return $this->generator;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    protected function getGeneratorDumperInstance(): GeneratorDumperInterface
    {
        return new $this->options['generator_dumper_class']($this->getRouteCollection());
    }

    protected function getMatcherDumperInstance(): MatcherDumperInterface
    {
        return new $this->options['matcher_dumper_class']($this->getRouteCollection());
    }

    
    private function getConfigCacheFactory(): ConfigCacheFactoryInterface
    {
        return $this->configCacheFactory ??= new ConfigCacheFactory($this->options['debug']);
    }

    private static function getCompiledRoutes(string $path): array
    {
        if ([] === self::$cache && \function_exists('opcache_invalidate') && filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN) && (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) || filter_var(\ini_get('opcache.enable_cli'), \FILTER_VALIDATE_BOOLEAN))) {
            self::$cache = null;
        }

        if (null === self::$cache) {
            return require $path;
        }

        return self::$cache[$path] ??= require $path;
    }
}
