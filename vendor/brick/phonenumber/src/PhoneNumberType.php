<?php

declare(strict_types=1);

namespace Brick\PhoneNumber;


final class PhoneNumberType
{
    
    public const FIXED_LINE = 0;

    
    public const MOBILE = 1;

    
    public const FIXED_LINE_OR_MOBILE = 2;

    
    public const TOLL_FREE = 3;

    
    public const PREMIUM_RATE = 4;

    
    public const SHARED_COST = 5;

    
    public const VOIP = 6;

    
    public const PERSONAL_NUMBER = 7;

    
    public const PAGER = 8;

    
    public const UAN = 9;

    
    public const UNKNOWN = 10;

    
    public const EMERGENCY = 27;

    
    public const VOICEMAIL = 28;

    
    public const SHORT_CODE = 29;

    
    public const STANDARD_RATE = 30;
}
