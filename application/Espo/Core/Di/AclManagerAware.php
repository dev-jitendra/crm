<?php


namespace Espo\Core\Di;

use Espo\Core\AclManager;

interface AclManagerAware
{
    public function setAclManager(AclManager $aclManager): void;
}
