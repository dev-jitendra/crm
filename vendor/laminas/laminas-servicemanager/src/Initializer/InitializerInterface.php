<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Initializer;

use Psr\Container\ContainerInterface;


interface InitializerInterface
{
    
    public function __invoke(ContainerInterface $container, $instance);
}
