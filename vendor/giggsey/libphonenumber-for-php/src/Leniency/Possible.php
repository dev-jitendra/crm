<?php

namespace libphonenumber\Leniency;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

class Possible extends AbstractLeniency
{
    protected static $level = 1;

    
    public static function verify(PhoneNumber $number, $candidate, PhoneNumberUtil $util)
    {
        return $util->isPossibleNumber($number);
    }
}
