<?php


namespace Espo\Core\Loaders;

use Espo\Core\AclManager as AclManagerService;
use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;

class AclManager implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function load(): AclManagerService
    {
        return $this->injectableFactory->create(AclManagerService::class);
    }
}
