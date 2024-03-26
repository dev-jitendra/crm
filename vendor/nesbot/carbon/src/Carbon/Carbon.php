<?php



namespace Carbon;

use Carbon\Traits\Date;
use Carbon\Traits\DeprecatedProperties;
use DateTime;
use DateTimeInterface;
use DateTimeZone;


class Carbon extends DateTime implements CarbonInterface
{
    use Date;

    
    public static function isMutable()
    {
        return true;
    }
}
