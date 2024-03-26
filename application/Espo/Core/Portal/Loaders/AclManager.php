<?php


namespace Espo\Core\Portal\Loaders;

use Espo\Core\AclManager as InternalAclManager;
use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\AclManager as PortalAclManager;

class AclManager implements Loader
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private InternalAclManager $internalAclManager
    ) {}

    public function load(): PortalAclManager
    {
        return $this->injectableFactory->createWith(PortalAclManager::class,[
            'internalAclManager' => $this->internalAclManager,
        ]);
    }
}
