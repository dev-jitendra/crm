<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;


interface AbstractFactoryInterface extends Factory\AbstractFactoryInterface
{
    
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName);

    
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName);
}
