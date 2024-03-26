<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use DateInterval;


final class DateIntervalHelper
{
    
    public static function toExcel(DateInterval $interval): float
    {
        
        
        $days = $interval->y * 365.25
            + $interval->m * 30.437
            + $interval->d
            + $interval->h / 24
            + $interval->i / 24 / 60
            + $interval->s / 24 / 60 / 60;

        if (1 === $interval->invert) {
            $days *= -1;
        }

        return $days;
    }
}
