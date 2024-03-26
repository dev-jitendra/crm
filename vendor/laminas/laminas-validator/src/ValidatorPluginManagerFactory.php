<?php

namespace Laminas\Validator;

use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function is_array;


class ValidatorPluginManagerFactory implements FactoryInterface
{
    
    protected $creationOptions;

    
    public function __invoke(ContainerInterface $container, $name, ?array $options = null)
    {
        $pluginManager = new ValidatorPluginManager($container, $options ?: []);

        
        
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        
        if (! $container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        
        if (! isset($config['validators']) || ! is_array($config['validators'])) {
            return $pluginManager;
        }

        
        (new Config($config['validators']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }

    
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: ValidatorPluginManager::class, $this->creationOptions);
    }

    
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
