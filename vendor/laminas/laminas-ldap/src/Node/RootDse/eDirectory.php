<?php

namespace Laminas\Ldap\Node\RootDse;

use Laminas\Ldap\Node;



class eDirectory extends Node\RootDse
{
    
    
    public function supportsExtension($oids)
    {
        return $this->attributeHasValue('supportedExtension', $oids);
    }

    
    public function getVendorName()
    {
        return $this->getAttribute('vendorName', 0);
    }

    
    public function getVendorVersion()
    {
        return $this->getAttribute('vendorVersion', 0);
    }

    
    public function getDsaName()
    {
        return $this->getAttribute('dsaName', 0);
    }

    
    public function getStatisticsErrors()
    {
        return $this->getAttribute('errors', 0);
    }

    
    public function getStatisticsSecurityErrors()
    {
        return $this->getAttribute('securityErrors', 0);
    }

    
    public function getStatisticsChainings()
    {
        return $this->getAttribute('chainings', 0);
    }

    
    public function getStatisticsReferralsReturned()
    {
        return $this->getAttribute('referralsReturned', 0);
    }

    
    public function getStatisticsExtendedOps()
    {
        return $this->getAttribute('extendedOps', 0);
    }

    
    public function getStatisticsAbandonOps()
    {
        return $this->getAttribute('abandonOps', 0);
    }

    
    public function getStatisticsWholeSubtreeSearchOps()
    {
        return $this->getAttribute('wholeSubtreeSearchOps', 0);
    }

    
    public function getServerType()
    {
        return self::SERVER_TYPE_EDIRECTORY;
    }
}
