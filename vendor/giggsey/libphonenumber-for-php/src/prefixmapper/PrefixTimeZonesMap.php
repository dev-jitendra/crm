<?php

namespace libphonenumber\prefixmapper;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

class PrefixTimeZonesMap
{
    
    const RAW_STRING_TIMEZONES_SEPARATOR = '&';
    protected $phonePrefixMap;

    public function __construct($map)
    {
        $this->phonePrefixMap = new PhonePrefixMap($map);
    }

    
    public function lookupTimeZonesForNumber(PhoneNumber $number)
    {
        $phonePrefix = $number->getCountryCode() . PhoneNumberUtil::getInstance()->getNationalSignificantNumber(
            $number
        );

        return $this->lookupTimeZonesForNumberKey($phonePrefix);
    }

    
    protected function lookupTimeZonesForNumberKey($key)
    {
        
        
        $timezonesString = $this->phonePrefixMap->lookupKey($key);

        if ($timezonesString === null) {
            return array();
        }

        return $this->tokenizeRawOutputString($timezonesString);
    }

    
    protected function tokenizeRawOutputString($timezonesString)
    {
        return explode(static::RAW_STRING_TIMEZONES_SEPARATOR, $timezonesString);
    }

    
    public function lookupCountryLevelTimeZonesForNumber(PhoneNumber $number)
    {
        return $this->lookupTimeZonesForNumberKey($number->getCountryCode());
    }
}
