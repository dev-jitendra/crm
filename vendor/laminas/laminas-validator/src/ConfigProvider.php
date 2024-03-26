<?php

namespace Laminas\Validator;

class ConfigProvider
{
    
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    
    public function getDependencyConfig()
    {
        return [
            'aliases'   => [
                'ValidatorManager' => ValidatorPluginManager::class,

                
                'Zend\Validator\ValidatorPluginManager' => ValidatorPluginManager::class,
            ],
            'factories' => [
                ValidatorPluginManager::class => ValidatorPluginManagerFactory::class,
            ],
        ];
    }
}
