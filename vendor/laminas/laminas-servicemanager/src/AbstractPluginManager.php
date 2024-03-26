<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Psr\Container\ContainerInterface;

use function class_exists;
use function gettype;
use function is_object;
use function method_exists;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;


abstract class AbstractPluginManager extends ServiceManager implements PluginManagerInterface
{
    
    protected $autoAddInvokableClass = true;

    
    protected $instanceOf;

    
    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        
        if (
            null !== $configInstanceOrParentLocator
            && ! $configInstanceOrParentLocator instanceof ConfigInterface
            && ! $configInstanceOrParentLocator instanceof ContainerInterface
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a ConfigInterface or ContainerInterface instance as the first argument; received %s',
                self::class,
                is_object($configInstanceOrParentLocator)
                    ? $configInstanceOrParentLocator::class
                    : gettype($configInstanceOrParentLocator)
            ));
        }

        if ($configInstanceOrParentLocator instanceof ConfigInterface) {
            trigger_error(sprintf(
                'Usage of %s as a constructor argument for %s is now deprecated',
                ConfigInterface::class,
                static::class
            ), E_USER_DEPRECATED);
            $config = $configInstanceOrParentLocator->toArray();
        }

        parent::__construct($config);

        if (! $configInstanceOrParentLocator instanceof ContainerInterface) {
            trigger_error(sprintf(
                '%s now expects a %s instance representing the parent container; please update your code',
                __METHOD__,
                ContainerInterface::class
            ), E_USER_DEPRECATED);
        }

        $this->creationContext = $configInstanceOrParentLocator instanceof ContainerInterface
            ? $configInstanceOrParentLocator
            : $this;
    }

    
    public function configure(array $config)
    {
        if (isset($config['services'])) {
            foreach ($config['services'] as $service) {
                $this->validate($service);
            }
        }

        parent::configure($config);

        return $this;
    }

    
    public function setService($name, $service)
    {
        $this->validate($service);
        parent::setService($name, $service);
    }

    
    public function get($name, ?array $options = null)
    {
        if (! $this->has($name)) {
            if (! $this->autoAddInvokableClass || ! class_exists($name)) {
                throw new Exception\ServiceNotFoundException(sprintf(
                    'A plugin by the name "%s" was not found in the plugin manager %s',
                    $name,
                    static::class
                ));
            }

            $this->setFactory($name, Factory\InvokableFactory::class);
        }

        $instance = ! $options ? parent::get($name) : $this->build($name, $options);
        $this->validate($instance);
        return $instance;
    }

    
    public function validate(mixed $instance)
    {
        if (method_exists($this, 'validatePlugin')) {
            trigger_error(sprintf(
                '%s::validatePlugin() has been deprecated as of 3.0; please define validate() instead',
                static::class
            ), E_USER_DEPRECATED);
            $this->validatePlugin($instance);
            return;
        }

        if (empty($this->instanceOf) || $instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            self::class,
            $this->instanceOf,
            is_object($instance) ? $instance::class : gettype($instance)
        ));
    }

    
    public function setServiceLocator(ContainerInterface $container)
    {
        trigger_error(sprintf(
            'Usage of %s is deprecated since v3.0.0; please pass the container to the constructor instead',
            __METHOD__
        ), E_USER_DEPRECATED);
        $this->creationContext = $container;
    }
}
