<?php

namespace Laminas\Mail\Header;

use function ord;
use function strlen;

final class HeaderName
{
    
    private function __construct()
    {
    }

    
    public static function filter($name)
    {
        $result = '';
        $tot    = strlen($name);
        for ($i = 0; $i < $tot; $i += 1) {
            $ord = ord($name[$i]);
            if ($ord > 32 && $ord < 127 && $ord !== 58) {
                $result .= $name[$i];
            }
        }
        return $result;
    }

    
    public static function isValid($name)
    {
        $tot = strlen($name);
        for ($i = 0; $i < $tot; $i += 1) {
            $ord = ord($name[$i]);
            if ($ord < 33 || $ord > 126 || $ord === 58) {
                return false;
            }
        }
        return true;
    }

    
    public static function assertValid($name)
    {
        if (! self::isValid($name)) {
            throw new Exception\RuntimeException('Invalid header name detected');
        }
    }
}
