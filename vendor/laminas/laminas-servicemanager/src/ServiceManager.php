<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use Exception;
use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Exception\CyclicAliasException;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Proxy\LazyServiceFactory;
use Laminas\Stdlib\ArrayUtils;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_intersect;
use function array_key_exists;
use function array_keys;
use function class_exists;
use function gettype;
use function in_array;
use function is_callable;
use function is_object;
use function is_string;
use function spl_autoload_register;
use function spl_object_hash;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;


class ServiceManager implements ServiceLocatorInterface
{
    
    protected $abstractFactories = [];

    
    protected $aliases = [];

    
    protected $allowOverride = false;

    
    protected $creationContext;

    
    protected $delegators = [];

    
    protected $factories = [];

    
    protected $initializers = [];

    
    protected $lazyServices = [];

    private ?LazyServiceFactory $lazyServicesDelegator = null;

    
    protected $services = [];

    
    protected $shared = [];

    
    protected $sharedByDefault = true;

    
    protected $configured = false;

    
    private array $cachedAbstractFactories = [];

    
    public function __construct(array $config = [])
    {
        $this->creationContext = $this;
        $this->configure($config);
    }

    
    public function getServiceLocator()
    {
        trigger_error(sprintf(
            'Usage of %s is deprecated since v3.0.0; please use the container passed to the factory instead',
            __METHOD__
        ), E_USER_DEPRECATED);
        return $this->creationContext;
    }

    
    public function get($name)
    {
        
        
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        
        $sharedService = $this->shared[$name] ?? $this->sharedByDefault;

        
        
        if (! $this->aliases) {
            $object = $this->doCreate($name);

            
            if ($sharedService) {
                $this->services[$name] = $object;
            }
            return $object;
        }

        
        $resolvedName = $this->aliases[$name] ?? $name;

        
        if ($resolvedName !== $name) {
            $sharedService = $this->shared[$resolvedName] ?? $sharedService;
        }

        
        $sharedAlias = $sharedService && isset($this->services[$resolvedName]);

        
        if ($sharedAlias) {
            $this->services[$name] = $this->services[$resolvedName];
            return $this->services[$resolvedName];
        }

        
        
        $object = $this->doCreate($resolvedName);

        
        if ($sharedService) {
            $this->services[$resolvedName] = $object;
        }

        
        
        if ($sharedAlias) {
            $this->services[$name] = $object;
        }

        return $object;
    }

    
    public function build($name, ?array $options = null)
    {
        
        $name = $this->aliases[$name] ?? $name;
        return $this->doCreate($name, $options);
    }

    
    public function has($name)
    {
        
        return $this->staticServiceOrFactoryCanCreate($name) || $this->abstractFactoryCanCreate($name);
    }

    
    public function setAllowOverride($flag)
    {
        $this->allowOverride = (bool) $flag;
    }

    
    public function getAllowOverride()
    {
        return $this->allowOverride;
    }

    
    public function configure(array $config)
    {
        
        
        $this->validateServiceNames($config);

        if (isset($config['services'])) {
            $this->services = $config['services'] + $this->services;
        }

        if (isset($config['invokables']) && ! empty($config['invokables'])) {
            $newAliases = $this->createAliasesAndFactoriesForInvokables($config['invokables']);
            
            
            $config['aliases'] = $newAliases + ($config['aliases'] ?? []);
        }

        if (isset($config['factories'])) {
            $this->factories = $config['factories'] + $this->factories;
        }

        if (isset($config['delegators'])) {
            $this->mergeDelegators($config['delegators']);
        }

        if (isset($config['shared'])) {
            $this->shared = $config['shared'] + $this->shared;
        }

        if (! empty($config['aliases'])) {
            $this->aliases = $config['aliases'] + $this->aliases;
            $this->mapAliasesToTargets();
        } elseif (! $this->configured && ! empty($this->aliases)) {
            $this->mapAliasesToTargets();
        }

        if (isset($config['shared_by_default'])) {
            $this->sharedByDefault = $config['shared_by_default'];
        }

        
        
        if (isset($config['lazy_services']) && ! empty($config['lazy_services'])) {
            
            $this->lazyServices          = ArrayUtils::merge($this->lazyServices, $config['lazy_services']);
            $this->lazyServicesDelegator = null;
        }

        
        
        if (isset($config['abstract_factories'])) {
            $abstractFactories = $config['abstract_factories'];
            
            foreach ($abstractFactories as $key => $abstractFactory) {
                $this->resolveAbstractFactoryInstance($abstractFactory);
            }
        }

        if (isset($config['initializers'])) {
            $this->resolveInitializers($config['initializers']);
        }

        $this->configured = true;

        return $this;
    }

    
    public function setAlias($alias, $target)
    {
        if (isset($this->services[$alias]) && ! $this->allowOverride) {
            throw ContainerModificationsNotAllowedException::fromExistingService($alias);
        }

        $this->mapAliasToTarget($alias, $target);
    }

    
    public function setInvokableClass($name, $class = null)
    {
        if (isset($this->services[$name]) && ! $this->allowOverride) {
            throw ContainerModificationsNotAllowedException::fromExistingService($name);
        }

        $this->createAliasesAndFactoriesForInvokables([$name => $class ?? $name]);
    }

    
    public function setFactory($name, $factory)
    {
        if (isset($this->services[$name]) && ! $this->allowOverride) {
            throw ContainerModificationsNotAllowedException::fromExistingService($name);
        }

        $this->factories[$name] = $factory;
    }

    
    public function mapLazyService($name, $class = null)
    {
        $this->configure(['lazy_services' => ['class_map' => [$name => $class ?: $name]]]);
    }

    
    public function addAbstractFactory($factory)
    {
        $this->resolveAbstractFactoryInstance($factory);
    }

    
    public function addDelegator($name, $factory)
    {
        $this->configure(['delegators' => [$name => [$factory]]]);
    }

    
    public function addInitializer($initializer)
    {
        $this->configure(['initializers' => [$initializer]]);
    }

    
    public function setService($name, $service)
    {
        if (isset($this->services[$name]) && ! $this->allowOverride) {
            throw ContainerModificationsNotAllowedException::fromExistingService($name);
        }
        $this->services[$name] = $service;
    }

    
    public function setShared($name, $flag)
    {
        if (isset($this->services[$name]) && ! $this->allowOverride) {
            throw ContainerModificationsNotAllowedException::fromExistingService($name);
        }

        $this->shared[$name] = (bool) $flag;
    }

    
    private function resolveInitializers(array $initializers): void
    {
        foreach ($initializers as $initializer) {
            if (is_string($initializer) && class_exists($initializer)) {
                $initializer = new $initializer();
            }

            if (is_callable($initializer)) {
                $this->initializers[] = $initializer;
                continue;
            }

            throw InvalidArgumentException::fromInvalidInitializer($initializer);
        }
    }

    
    private function getFactory(string $name): callable
    {
        $factory = $this->factories[$name] ?? null;

        $lazyLoaded = false;
        if (is_string($factory) && class_exists($factory)) {
            $factory    = new $factory();
            $lazyLoaded = true;
        }

        if (is_callable($factory)) {
            if ($lazyLoaded) {
                $this->factories[$name] = $factory;
            }

            return $factory;
        }

        
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canCreate($this->creationContext, $name)) {
                return $abstractFactory;
            }
        }

        throw new ServiceNotFoundException(sprintf(
            'Unable to resolve service "%s" to a factory; are you certain you provided it during configuration?',
            $name
        ));
    }

    
    private function createDelegatorFromName(string $name, ?array $options = null)
    {
        $creationCallback = function () use ($name, $options) {
            
            $factory = $this->getFactory($name);
            return $factory($this->creationContext, $name, $options);
        };

        $initialCreationContext = $this->creationContext;

        foreach ($this->delegators[$name] as $index => $delegatorFactory) {
            $delegatorFactory = $this->delegators[$name][$index];

            if ($delegatorFactory === LazyServiceFactory::class) {
                $delegatorFactory = $this->createLazyServiceDelegatorFactory();
            } elseif (is_string($delegatorFactory) && class_exists($delegatorFactory)) {
                $delegatorFactory = new $delegatorFactory();
            }

            $this->assertCallableDelegatorFactory($delegatorFactory);

            $this->delegators[$name][$index] = $delegatorFactory;

            $creationCallback =
                
                static fn() => $delegatorFactory($initialCreationContext, $name, $creationCallback, $options);
        }

        return $creationCallback();
    }

    
    private function doCreate(string $resolvedName, ?array $options = null)
    {
        try {
            if (! isset($this->delegators[$resolvedName])) {
                
                $factory = $this->getFactory($resolvedName);
                $object  = $factory($this->creationContext, $resolvedName, $options);
            } else {
                $object = $this->createDelegatorFromName($resolvedName, $options);
            }
        } catch (ContainerExceptionInterface $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new ServiceNotCreatedException(sprintf(
                'Service with name "%s" could not be created. Reason: %s',
                $resolvedName,
                $exception->getMessage()
            ), (int) $exception->getCode(), $exception);
        }

        foreach ($this->initializers as $initializer) {
            $initializer($this->creationContext, $object);
        }

        return $object;
    }

    
    private function createLazyServiceDelegatorFactory(): LazyServiceFactory
    {
        if ($this->lazyServicesDelegator) {
            return $this->lazyServicesDelegator;
        }

        if (! isset($this->lazyServices['class_map'])) {
            throw new ServiceNotCreatedException('Missing "class_map" config key in "lazy_services"');
        }

        $factoryConfig = new ProxyConfiguration();

        if (isset($this->lazyServices['proxies_namespace'])) {
            $factoryConfig->setProxiesNamespace($this->lazyServices['proxies_namespace']);
        }

        if (isset($this->lazyServices['proxies_target_dir'])) {
            $factoryConfig->setProxiesTargetDir($this->lazyServices['proxies_target_dir']);
        }

        if (! isset($this->lazyServices['write_proxy_files']) || ! $this->lazyServices['write_proxy_files']) {
            $factoryConfig->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        } else {
            $factoryConfig->setGeneratorStrategy(new FileWriterGeneratorStrategy(
                new FileLocator($factoryConfig->getProxiesTargetDir())
            ));
        }

        spl_autoload_register($factoryConfig->getProxyAutoloader());

        $this->lazyServicesDelegator = new LazyServiceFactory(
            new LazyLoadingValueHolderFactory($factoryConfig),
            $this->lazyServices['class_map']
        );

        return $this->lazyServicesDelegator;
    }

    
    private function mergeDelegators(array $config): array
    {
        foreach ($config as $key => $delegators) {
            if (! array_key_exists($key, $this->delegators)) {
                $this->delegators[$key] = $delegators;
                continue;
            }

            foreach ($delegators as $delegator) {
                if (! in_array($delegator, $this->delegators[$key], true)) {
                    $this->delegators[$key][] = $delegator;
                }
            }
        }

        return $this->delegators;
    }

    
    private function createAliasesAndFactoriesForInvokables(array $invokables): array
    {
        $newAliases = [];

        foreach ($invokables as $name => $class) {
            $this->factories[$class] = Factory\InvokableFactory::class;
            if ($name !== $class) {
                $this->aliases[$name] = $class;
                $newAliases[$name]    = $class;
            }
        }

        return $newAliases;
    }

    
    private function validateServiceNames(array $config): void
    {
        if ($this->allowOverride || ! $this->configured) {
            return;
        }

        if (isset($config['services'])) {
            foreach (array_keys($config['services']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['aliases'])) {
            foreach (array_keys($config['aliases']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['invokables'])) {
            foreach (array_keys($config['invokables']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['factories'])) {
            foreach (array_keys($config['factories']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['delegators'])) {
            foreach (array_keys($config['delegators']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['shared'])) {
            foreach (array_keys($config['shared']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }

        if (isset($config['lazy_services']['class_map'])) {
            foreach (array_keys($config['lazy_services']['class_map']) as $service) {
                if (isset($this->services[$service])) {
                    throw ContainerModificationsNotAllowedException::fromExistingService($service);
                }
            }
        }
    }

    
    private function mapAliasToTarget(string $alias, string $target): void
    {
        
        
        $this->aliases[$alias] = $this->aliases[$target] ?? $target;

        
        if ($alias === $this->aliases[$alias]) {
            throw CyclicAliasException::fromCyclicAlias($alias, $this->aliases);
        }

        
        
        if (in_array($alias, $this->aliases)) {
            $r = array_intersect($this->aliases, [$alias]);
            
            foreach ($r as $name => $service) {
                $this->aliases[$name] = $target;
            }
        }
    }

    
    private function mapAliasesToTargets(): void
    {
        $tagged = [];
        foreach ($this->aliases as $alias => $target) {
            if (isset($tagged[$alias])) {
                continue;
            }

            $tCursor = $this->aliases[$alias];
            $aCursor = $alias;
            if ($aCursor === $tCursor) {
                throw CyclicAliasException::fromCyclicAlias($alias, $this->aliases);
            }
            if (! isset($this->aliases[$tCursor])) {
                continue;
            }

            $stack = [];

            while (isset($this->aliases[$tCursor])) {
                $stack[] = $aCursor;
                if ($aCursor === $this->aliases[$tCursor]) {
                    throw CyclicAliasException::fromCyclicAlias($alias, $this->aliases);
                }
                $aCursor = $tCursor;
                $tCursor = $this->aliases[$tCursor];
            }

            $tagged[$aCursor] = true;

            foreach ($stack as $alias) {
                if ($alias === $tCursor) {
                    throw CyclicAliasException::fromCyclicAlias($alias, $this->aliases);
                }
                $this->aliases[$alias] = $tCursor;
                $tagged[$alias]        = true;
            }
        }
    }

    
    private function resolveAbstractFactoryInstance($abstractFactory): void
    {
        if (is_string($abstractFactory) && class_exists($abstractFactory)) {
            
            if (! isset($this->cachedAbstractFactories[$abstractFactory])) {
                $this->cachedAbstractFactories[$abstractFactory] = new $abstractFactory();
            }

            $abstractFactory = $this->cachedAbstractFactories[$abstractFactory];
        }

        if (! $abstractFactory instanceof Factory\AbstractFactoryInterface) {
            throw InvalidArgumentException::fromInvalidAbstractFactory($abstractFactory);
        }

        $abstractFactoryObjHash                           = spl_object_hash($abstractFactory);
        $this->abstractFactories[$abstractFactoryObjHash] = $abstractFactory;
    }

    
    private function staticServiceOrFactoryCanCreate(string $name): bool
    {
        if (isset($this->services[$name]) || isset($this->factories[$name])) {
            return true;
        }

        $resolvedName = $this->aliases[$name] ?? $name;
        if ($resolvedName !== $name) {
            return $this->staticServiceOrFactoryCanCreate($resolvedName);
        }

        return false;
    }

    
    private function abstractFactoryCanCreate(string $name): bool
    {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canCreate($this->creationContext, $name)) {
                return true;
            }
        }

        $resolvedName = $this->aliases[$name] ?? $name;
        if ($resolvedName !== $name) {
            return $this->abstractFactoryCanCreate($resolvedName);
        }

        return false;
    }

    
    private function assertCallableDelegatorFactory($delegatorFactory): void
    {
        if (
            $delegatorFactory instanceof Factory\DelegatorFactoryInterface
            || is_callable($delegatorFactory)
        ) {
            return;
        }
        if (is_string($delegatorFactory)) {
            throw new ServiceNotCreatedException(sprintf(
                'An invalid delegator factory was registered; resolved to class or function "%s"'
                . ' which does not exist; please provide a valid function name or class name resolving'
                . ' to an implementation of %s',
                $delegatorFactory,
                DelegatorFactoryInterface::class
            ));
        }
        throw new ServiceNotCreatedException(sprintf(
            'A non-callable delegator, "%s", was provided; expected a callable or instance of "%s"',
            is_object($delegatorFactory) ? $delegatorFactory::class : gettype($delegatorFactory),
            DelegatorFactoryInterface::class
        ));
    }
}
