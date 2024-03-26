<?php


namespace Espo\Core\Portal\Loaders;

use Espo\Core\Portal\AclManager as PortalAclManager;
use Espo\Core\AclManager;
use Espo\Core\Container\Loader;
use Espo\Core\Portal\Acl as AclService;
use Espo\Entities\User;

use InvalidArgumentException;

class Acl implements Loader
{
    private PortalAclManager $aclManager;
    private User $user;

    public function __construct(AclManager $aclManager, User $user)
    {
        if (!$aclManager instanceof PortalAclManager) {
            throw new InvalidArgumentException();
        }

        $this->aclManager = $aclManager;
        $this->user = $user;
    }

    public function load(): AclService
    {
        return new AclService($this->aclManager, $this->user);
    }
}
