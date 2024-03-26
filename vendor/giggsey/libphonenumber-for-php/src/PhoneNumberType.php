<?php

namespace libphonenumber;


class PhoneNumberType
{
    const FIXED_LINE = 0;
    const MOBILE = 1;
    
    
    const FIXED_LINE_OR_MOBILE = 2;
    
    const TOLL_FREE = 3;
    const PREMIUM_RATE = 4;
    
    
    
    const SHARED_COST = 5;
    
    const VOIP = 6;
    
    
    
    const PERSONAL_NUMBER = 7;
    const PAGER = 8;
    
    
    const UAN = 9;
    
    
    const UNKNOWN = 10;

    
    const EMERGENCY = 27;

    
    const VOICEMAIL = 28;

    
    const SHORT_CODE = 29;

    
    const STANDARD_RATE = 30;

    public static function values()
    {
        return array(
            self::FIXED_LINE => 'FIXED_LINE',
            self::MOBILE => 'MOBILE',
            self::FIXED_LINE_OR_MOBILE => 'FIXED_LINE_OR_MOBILE',
            self::TOLL_FREE => 'TOLL_FREE',
            self::PREMIUM_RATE => 'PREMIUM_RATE',
            self::SHARED_COST => 'SHARED_COST',
            self::VOIP => 'VOIP',
            self::PERSONAL_NUMBER => 'PERSONAL_NUMBER',
            self::PAGER => 'PAGER',
            self::UAN => 'UAN',
            self::UNKNOWN => 'UNKNOWN',
            self::EMERGENCY => 'EMERGENCY',
            self::VOICEMAIL => 'VOICEMAIL',
            self::SHORT_CODE => 'SHORT_CODE',
            self::STANDARD_RATE => 'STANDARD_RATE',
        );
    }
}
