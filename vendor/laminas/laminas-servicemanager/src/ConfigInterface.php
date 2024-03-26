<?php

declare(strict_types=1);

namespace Laminas\ServiceManager;

use ArrayAccess;
use Psr\Container\ContainerInterface;


interface ConfigInterface
{
    
    public function configureServiceManager(ServiceManager $serviceManager);

    
    public function toArray();
}
