<?php

namespace Laminas\Ldap\Node\RootDse;

use Laminas\Ldap;
use Laminas\Ldap\Dn;
use Laminas\Ldap\Node;


class ActiveDirectory extends Node\RootDse
{
    
    public function getConfigurationNamingContext()
    {
        return $this->getAttribute('configurationNamingContext', 0);
    }

    
    public function getCurrentTime()
    {
        return $this->getAttribute('currentTime', 0);
    }

    
    public function getDefaultNamingContext()
    {
        return $this->getAttribute('defaultNamingContext', 0);
    }

    
    public function getDnsHostName()
    {
        return $this->getAttribute('dnsHostName', 0);
    }

    
    public function getDomainControllerFunctionality()
    {
        return $this->getAttribute('domainControllerFunctionality', 0);
    }

    
    public function getDomainFunctionality()
    {
        return $this->getAttribute('domainFunctionality', 0);
    }

    
    public function getDsServiceName()
    {
        return $this->getAttribute('dsServiceName', 0);
    }

    
    public function getForestFunctionality()
    {
        return $this->getAttribute('forestFunctionality', 0);
    }

    
    public function getHighestCommittedUSN()
    {
        return $this->getAttribute('highestCommittedUSN', 0);
    }

    
    public function getIsGlobalCatalogReady()
    {
        return $this->getAttribute('isGlobalCatalogReady', 0);
    }

    
    public function getIsSynchronized()
    {
        return $this->getAttribute('isSynchronized', 0);
    }

    
    public function getLDAPServiceName()
    {
        return $this->getAttribute('ldapServiceName', 0);
    }

    
    public function getRootDomainNamingContext()
    {
        return $this->getAttribute('rootDomainNamingContext', 0);
    }

    
    public function getSchemaNamingContext()
    {
        return $this->getAttribute('schemaNamingContext', 0);
    }

    
    public function getServerName()
    {
        return $this->getAttribute('serverName', 0);
    }

    
    public function supportsCapability($oids)
    {
        return $this->attributeHasValue('supportedCapabilities', $oids);
    }

    
    public function supportsControl($oids)
    {
        return $this->attributeHasValue('supportedControl', $oids);
    }

    
    public function supportsPolicy($policies)
    {
        return $this->attributeHasValue('supportedLDAPPolicies', $policies);
    }

    
    public function getServerType()
    {
        return self::SERVER_TYPE_ACTIVEDIRECTORY;
    }

    
    public function getSchemaDn()
    {
        $schemaDn = $this->getSchemaNamingContext();
        return Ldap\Dn::fromString($schemaDn);
    }
}
