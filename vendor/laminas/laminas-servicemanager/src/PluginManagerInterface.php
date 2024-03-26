<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use Laminas\ServiceManager\Exception\InvalidServiceException;
use Psr\Container\ContainerExceptionInterface;


interface PluginManagerInterface extends ServiceLocatorInterface
{
    
    public function validate(mixed $instance);
}
