<?php

namespace Laminas\Ldap\Node\Schema\ObjectClass;

use Laminas\Ldap\Node\Schema;


class ActiveDirectory extends Schema\AbstractItem implements ObjectClassInterface
{
    
    public function getName()
    {
        return $this->ldapdisplayname[0];
    }

    
    public function getOid()
    {
        return null;
    }

    
    public function getMustContain()
    {
        return null;
    }

    
    public function getMayContain()
    {
        return null;
    }

    
    public function getDescription()
    {
        return null;
    }

    
    public function getType()
    {
        return null;
    }

    
    public function getParentClasses()
    {
        return null;
    }
}
