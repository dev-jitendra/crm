<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Helper;

use DateInterval;

final class DateIntervalFormatHelper
{
    
    private const dateIntervalFormats = [
        'hh' => '%H',
        'h' => '%h',
        'mm' => '%I',
        'm' => '%i',
        'ss' => '%S',
        's' => '%s',
    ];

    
    public static function createDateIntervalFromHours(float $dayFractions): DateInterval
    {
        $time = abs($dayFractions) * 24; 
        $hours = floor($time);
        $time = ($time - $hours) * 60;
        $minutes = (int) floor($time); 
        $time = ($time - $minutes) * 60;
        $seconds = (int) round($time); 

        
        if (60 === $seconds) {
            $seconds = 0;
            ++$minutes;
        }
        if (60 === $minutes) {
            $minutes = 0;
            ++$hours;
        }

        $interval = new DateInterval("P0DT{$hours}H{$minutes}M{$seconds}S");
        if ($dayFractions < 0) {
            $interval->invert = 1;
        }

        return $interval;
    }

    public static function isDurationFormat(string $excelFormat): bool
    {
        
        return 1 === preg_match('/^(\[hh?](:mm(:ss)?)?|\[mm?](:ss)?|\[ss?])$/', $excelFormat);
    }

    public static function toPHPDateIntervalFormat(string $excelDateFormat, ?string &$startUnit = null): string
    {
        $startUnit = null;
        $phpFormatParts = [];
        $formatParts = explode(':', str_replace(['[', ']'], '', $excelDateFormat));
        foreach ($formatParts as $formatPart) {
            $startUnit ??= $formatPart;
            $phpFormatParts[] = self::dateIntervalFormats[$formatPart];
        }

        
        return '%r'.implode(':', $phpFormatParts);
    }

    public static function formatDateInterval(DateInterval $dateInterval, string $excelDateFormat): string
    {
        $phpFormat = self::toPHPDateIntervalFormat($excelDateFormat, $startUnit);

        
        $startUnit = $startUnit[0]; 
        $dateIntervalClone = clone $dateInterval;
        if ('m' === $startUnit) {
            $dateIntervalClone->i = $dateIntervalClone->i + $dateIntervalClone->h * 60;
            $dateIntervalClone->h = 0;
        } elseif ('s' === $startUnit) {
            $dateIntervalClone->s = $dateIntervalClone->s + $dateIntervalClone->i * 60 + $dateIntervalClone->h * 3600;
            $dateIntervalClone->i = 0;
            $dateIntervalClone->h = 0;
        }

        return $dateIntervalClone->format($phpFormat);
    }
}
