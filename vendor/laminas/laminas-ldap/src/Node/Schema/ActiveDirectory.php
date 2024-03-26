<?php

namespace Laminas\Ldap\Node\Schema;

use Laminas\Ldap;
use Laminas\Ldap\Node;


class ActiveDirectory extends Node\Schema
{
    
    protected $attributeTypes = [];
    
    protected $objectClasses = [];

    
    protected function parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        parent::parseSchema($dn, $ldap);
        foreach (
            $ldap->search(
                '(objectClass=classSchema)',
                $dn,
                Ldap\Ldap::SEARCH_SCOPE_ONE
            ) as $node
        ) {
            $val                                  = new ObjectClass\ActiveDirectory($node);
            $this->objectClasses[$val->getName()] = $val;
        }
        foreach (
            $ldap->search(
                '(objectClass=attributeSchema)',
                $dn,
                Ldap\Ldap::SEARCH_SCOPE_ONE
            ) as $node
        ) {
            $val                                   = new AttributeType\ActiveDirectory($node);
            $this->attributeTypes[$val->getName()] = $val;
        }

        return $this;
    }

    
    public function getAttributeTypes()
    {
        return $this->attributeTypes;
    }

    
    public function getObjectClasses()
    {
        return $this->objectClasses;
    }
}
