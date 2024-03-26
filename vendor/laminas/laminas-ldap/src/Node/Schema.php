<?php

namespace Laminas\Ldap\Node;

use Laminas\Ldap;


class Schema extends AbstractNode
{
    public const OBJECTCLASS_TYPE_UNKNOWN    = 0;
    public const OBJECTCLASS_TYPE_STRUCTURAL = 1;
    public const OBJECTCLASS_TYPE_ABSTRACT   = 3;
    public const OBJECTCLASS_TYPE_AUXILIARY  = 4;

    
    public static function create(Ldap\Ldap $ldap)
    {
        $dn   = $ldap->getRootDse()->getSchemaDn();
        $data = $ldap->getEntry($dn, ['*', '+'], true);
        switch ($ldap->getRootDse()->getServerType()) {
            case RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                return new Schema\ActiveDirectory($dn, $data, $ldap);
            case RootDse::SERVER_TYPE_OPENLDAP:
                return new Schema\OpenLdap($dn, $data, $ldap);
            case RootDse::SERVER_TYPE_EDIRECTORY:
            default:
                return new static($dn, $data, $ldap);
        }
    }

    
    protected function __construct(Ldap\Dn $dn, array $data, Ldap\Ldap $ldap)
    {
        parent::__construct($dn, $data, true);
        $this->parseSchema($dn, $ldap);
    }

    
    protected function parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        return $this;
    }

    
    public function getAttributeTypes()
    {
        return [];
    }

    
    public function getObjectClasses()
    {
        return [];
    }
}
