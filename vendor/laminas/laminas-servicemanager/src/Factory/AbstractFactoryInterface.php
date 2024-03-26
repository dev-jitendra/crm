<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Factory;

use Psr\Container\ContainerInterface;


interface AbstractFactoryInterface extends FactoryInterface
{
    
    public function canCreate(ContainerInterface $container, $requestedName);
}
