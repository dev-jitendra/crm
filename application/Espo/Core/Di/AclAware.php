<?php


namespace Espo\Core\Di;

use Espo\Core\Acl;

interface AclAware
{
    public function setAcl(Acl $acl): void;
}
