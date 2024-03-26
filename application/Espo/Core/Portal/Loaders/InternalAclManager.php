<?php


namespace Espo\Core\Portal\Loaders;

use Espo\Core\AclManager as InternalAclManagerService;
use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;

class InternalAclManager implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function load(): InternalAclManagerService
    {
        return $this->injectableFactory->create(InternalAclManagerService::class);
    }
}
