<?php

namespace Laminas\Validator;

use Laminas\ModuleManager\ModuleManager;

class Module
{
    
    public function getConfig()
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }

    
    public function init($moduleManager)
    {
        $event           = $moduleManager->getEvent();
        $container       = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'ValidatorManager',
            'validators',
            ValidatorProviderInterface::class,
            'getValidatorConfig'
        );
    }
}
