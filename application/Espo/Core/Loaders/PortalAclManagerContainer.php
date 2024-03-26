<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\AclManagerContainer as PortalAclManagerContainerService;

class PortalAclManagerContainer implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory) {}

    public function load(): PortalAclManagerContainerService
    {
        return new PortalAclManagerContainerService($this->injectableFactory);
    }
}
