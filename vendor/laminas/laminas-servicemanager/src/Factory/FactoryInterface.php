<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Factory;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;


interface FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null);
}
