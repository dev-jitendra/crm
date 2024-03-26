<?php

namespace libphonenumber\Leniency;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberMatcher;
use libphonenumber\PhoneNumberUtil;

class Valid extends AbstractLeniency
{
    protected static $level = 2;

    
    public static function verify(PhoneNumber $number, $candidate, PhoneNumberUtil $util)
    {
        if (!$util->isValidNumber($number)
            || !PhoneNumberMatcher::containsOnlyValidXChars($number, $candidate, $util)) {
            return false;
        }

        return PhoneNumberMatcher::isNationalPrefixPresentIfRequired($number, $util);
    }
}
