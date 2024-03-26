<?php

namespace Laminas\Mail;

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
                'Zend\Mail\Protocol\SmtpPluginManager' => Protocol\SmtpPluginManager::class,
            ],
            'factories' => [
                Protocol\SmtpPluginManager::class => Protocol\SmtpPluginManagerFactory::class,
            ],
        ];
    }
}
