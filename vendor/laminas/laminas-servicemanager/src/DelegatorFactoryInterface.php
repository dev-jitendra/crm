<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;


interface DelegatorFactoryInterface extends Factory\DelegatorFactoryInterface
{
    
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback);
}
