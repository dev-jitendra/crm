<?php


namespace Espo\Core\Loaders;

use Espo\Core\AclManager as AclManagerService;
use Espo\Core\Container;
use Espo\Core\Container\Loader;

class InternalAclManager implements Loader
{
    public function __construct(private Container $container)
    {}

    public function load(): AclManagerService
    {
        return $this->container->getByClass(AclManagerService::class);
    }
}
