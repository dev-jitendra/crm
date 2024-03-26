<?php

namespace Laminas\Mail\Protocol;


use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;


class SmtpPluginManagerFactory implements FactoryInterface
{
    
    protected $creationOptions;

    
    public function __invoke(ContainerInterface $container, $name, ?array $options = null)
    {
        return new SmtpPluginManager($container, $options ?: []);
    }

    
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: SmtpPluginManager::class, $this->creationOptions);
    }

    
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
