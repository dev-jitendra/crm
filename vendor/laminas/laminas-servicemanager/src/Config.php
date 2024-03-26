<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use Laminas\Stdlib\ArrayUtils;

use function array_keys;


class Config implements ConfigInterface
{
    
    private array $allowedKeys = [
        'abstract_factories' => true,
        'aliases'            => true,
        'delegators'         => true,
        'factories'          => true,
        'initializers'       => true,
        'invokables'         => true,
        'lazy_services'      => true,
        'services'           => true,
        'shared'             => true,
    ];

    
    protected $config = [
        'abstract_factories' => [],
        'aliases'            => [],
        'delegators'         => [],
        'factories'          => [],
        'initializers'       => [],
        'invokables'         => [],
        'lazy_services'      => [],
        'services'           => [],
        'shared'             => [],
    ];

    
    public function __construct(array $config = [])
    {
        
        foreach (array_keys($config) as $key) {
            if (! isset($this->allowedKeys[$key])) {
                unset($config[$key]);
            }
        }

        
        $this->config = $this->merge($this->config, $config);
    }

    
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        return $serviceManager->configure($this->config);
    }

    
    public function toArray()
    {
        return $this->config;
    }

    
    private function merge(array $a, array $b)
    {
        return ArrayUtils::merge($a, $b);
    }
}
