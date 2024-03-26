<?php

namespace Laminas\Ldap\Node;

use Laminas\Ldap;
use Laminas\Ldap\Dn;


class RootDse extends AbstractNode
{
    public const SERVER_TYPE_GENERIC         = 1;
    public const SERVER_TYPE_OPENLDAP        = 2;
    public const SERVER_TYPE_ACTIVEDIRECTORY = 3;
    public const SERVER_TYPE_EDIRECTORY      = 4;

    
    public static function create(Ldap\Ldap $ldap)
    {
        $dn   = Ldap\Dn::fromString('');
        $data = $ldap->getEntry($dn, ['*', '+'], true);
        if (isset($data['domainfunctionality'])) {
            return new RootDse\ActiveDirectory($dn, $data);
        } elseif (isset($data['dsaname'])) {
            return new RootDse\eDirectory($dn, $data);
        } elseif (
            isset($data['structuralobjectclass'])
            && $data['structuralobjectclass'][0] === 'OpenLDAProotDSE'
        ) {
            return new RootDse\OpenLdap($dn, $data);
        }

        return new static($dn, $data);
    }

    
    protected function __construct(Ldap\Dn $dn, array $data)
    {
        parent::__construct($dn, $data, true);
    }

    
    public function getNamingContexts()
    {
        return $this->getAttribute('namingContexts', null);
    }

    
    public function getSubschemaSubentry()
    {
        return $this->getAttribute('subschemaSubentry', 0);
    }

    
    public function supportsVersion($versions)
    {
        return $this->attributeHasValue('supportedLDAPVersion', $versions);
    }

    
    public function supportsSaslMechanism($mechlist)
    {
        return $this->attributeHasValue('supportedSASLMechanisms', $mechlist);
    }

    
    public function getServerType()
    {
        return self::SERVER_TYPE_GENERIC;
    }

    
    public function getSchemaDn()
    {
        $schemaDn = $this->getSubschemaSubentry();
        return Ldap\Dn::fromString($schemaDn);
    }
}
