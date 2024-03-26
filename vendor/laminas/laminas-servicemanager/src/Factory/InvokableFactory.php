<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Factory;

use Psr\Container\ContainerInterface;


final class InvokableFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return null === $options ? new $requestedName() : new $requestedName($options);
    }
}
