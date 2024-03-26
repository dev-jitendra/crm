<?php

namespace Laminas\Ldap\Node\Schema\AttributeType;

use Laminas\Ldap\Node\Schema;


class ActiveDirectory extends Schema\AbstractItem implements AttributeTypeInterface
{
    
    public function getName()
    {
        return $this->ldapdisplayname[0];
    }

    
    public function getOid()
    {
        return null;
    }

    
    public function getSyntax()
    {
        return null;
    }

    
    public function getMaxLength()
    {
        return null;
    }

    
    public function isSingleValued()
    {
        return null;
    }

    
    public function getDescription()
    {
        return null;
    }
}
