<?php

namespace libphonenumber\Leniency;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

abstract class AbstractLeniency
{
    
    protected static $level;

    
    public static function verify(PhoneNumber $number, $candidate, PhoneNumberUtil $util)
    {
        
        throw new \BadMethodCallException;
    }

    
    public static function compareTo(AbstractLeniency $leniency)
    {
        return static::getLevel() - $leniency::getLevel();
    }

    protected static function getLevel()
    {
        if (static::$level === null) {
            throw new \RuntimeException('$level should be defined');
        }

        return static::$level;
    }

    public function __toString()
    {
        return str_replace('libphonenumber\\Leniency\\', '', get_class($this));
    }
}
