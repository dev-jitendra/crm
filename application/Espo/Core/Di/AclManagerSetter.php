<?php


namespace Espo\Core\Di;

use Espo\Core\AclManager;

trait AclManagerSetter
{
    
    protected $aclManager;

    public function setAclManager(AclManager $aclManager): void
    {
        $this->aclManager = $aclManager;
    }
}
