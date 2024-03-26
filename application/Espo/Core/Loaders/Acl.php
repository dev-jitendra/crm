<?php


namespace Espo\Core\Loaders;

use Espo\Core\Acl as AclService;
use Espo\Core\AclManager;
use Espo\Core\Container\Loader;
use Espo\Entities\User;

class Acl implements Loader
{
    public function __construct(private AclManager $aclManager, private User $user)
    {}

    public function load(): AclService
    {
        return new AclService($this->aclManager, $this->user);
    }
}
