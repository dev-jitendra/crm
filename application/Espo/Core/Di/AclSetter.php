<?php


namespace Espo\Core\Di;

use Espo\Core\Acl;

trait AclSetter
{
    
    protected $acl;

    public function setAcl(Acl $acl): void
    {
        $this->acl = $acl;
    }
}
