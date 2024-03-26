<?php

namespace Laminas\Ldap;

use LDAP\Connection;
use LDAP\Result;
use LDAP\ResultEntry;


class Handler
{
    
    public static function isLdapHandle($handle)
    {
        return $handle instanceof Connection;
    }

    
    public static function isResultHandle($handle)
    {
        return $handle instanceof Result;
    }

    
    public static function isResultEntryHandle($handle)
    {
        return $handle instanceof ResultEntry;
    }
}
