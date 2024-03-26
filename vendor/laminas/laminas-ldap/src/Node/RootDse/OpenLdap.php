<?php

namespace Laminas\Ldap\Node\RootDse;

use Laminas\Ldap\Node;


class OpenLdap extends Node\RootDse
{
    
    public function getConfigContext()
    {
        return $this->getAttribute('configContext', 0);
    }

    
    public function getMonitorContext()
    {
        return $this->getAttribute('monitorContext', 0);
    }

    
    public function supportsControl($oids)
    {
        return $this->attributeHasValue('supportedControl', $oids);
    }

    
    public function supportsExtension($oids)
    {
        return $this->attributeHasValue('supportedExtension', $oids);
    }

    
    public function supportsFeature($oids)
    {
        return $this->attributeHasValue('supportedFeatures', $oids);
    }

    
    public function getServerType()
    {
        return self::SERVER_TYPE_OPENLDAP;
    }
}
