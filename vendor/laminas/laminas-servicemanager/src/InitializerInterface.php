<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;


interface InitializerInterface extends Initializer\InitializerInterface
{
    
    public function initialize(mixed $instance, ServiceLocatorInterface $serviceLocator);
}
