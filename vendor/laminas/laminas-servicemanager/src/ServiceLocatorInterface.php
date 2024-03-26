<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;


interface ServiceLocatorInterface extends ContainerInterface
{
    
    public function build($name, ?array $options = null);
}
