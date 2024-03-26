<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;


interface FactoryInterface extends Factory\FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator);
}
