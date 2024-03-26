<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use DateTimeInterface;


final class DateHelper
{
    
    public static function toExcel(DateTimeInterface $dateTime): float
    {
        $year = (int) $dateTime->format('Y');
        $month = (int) $dateTime->format('m');
        $day = (int) $dateTime->format('d');
        $hours = (int) $dateTime->format('H');
        $minutes = (int) $dateTime->format('i');
        $seconds = (int) $dateTime->format('s');
        
        
        $excel1900isLeapYear = 1;
        if ((1900 === $year) && ($month <= 2)) {
            $excel1900isLeapYear = 0;
        }
        $myexcelBaseDate = 2415020;

        
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        
        $century = (int) substr((string) $year, 0, 2);
        $decade = (int) substr((string) $year, 2, 2);
        $excelDate =
            floor((146097 * $century) / 4)
            + floor((1461 * $decade) / 4)
            + floor((153 * $month + 2) / 5)
            + $day
            + 1721119
            - $myexcelBaseDate
            + $excel1900isLeapYear;

        $excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

        return $excelDate + $excelTime;
    }
}
