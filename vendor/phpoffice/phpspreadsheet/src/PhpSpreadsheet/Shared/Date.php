<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use DateTimeInterface;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Date
{
    
    const CALENDAR_WINDOWS_1900 = 1900; 
    const CALENDAR_MAC_1904 = 1904; 

    
    public static $monthNames = [
        'Jan' => 'January',
        'Feb' => 'February',
        'Mar' => 'March',
        'Apr' => 'April',
        'May' => 'May',
        'Jun' => 'June',
        'Jul' => 'July',
        'Aug' => 'August',
        'Sep' => 'September',
        'Oct' => 'October',
        'Nov' => 'November',
        'Dec' => 'December',
    ];

    
    public static $numberSuffixes = [
        'st',
        'nd',
        'rd',
        'th',
    ];

    
    protected static $excelCalendar = self::CALENDAR_WINDOWS_1900;

    
    protected static $defaultTimeZone;

    
    public static function setExcelCalendar($baseDate)
    {
        if (
            ($baseDate == self::CALENDAR_WINDOWS_1900) ||
            ($baseDate == self::CALENDAR_MAC_1904)
        ) {
            self::$excelCalendar = $baseDate;

            return true;
        }

        return false;
    }

    
    public static function getExcelCalendar()
    {
        return self::$excelCalendar;
    }

    
    public static function setDefaultTimezone($timeZone)
    {
        try {
            $timeZone = self::validateTimeZone($timeZone);
            self::$defaultTimeZone = $timeZone;
            $retval = true;
        } catch (PhpSpreadsheetException $e) {
            $retval = false;
        }

        return $retval;
    }

    
    public static function getDefaultTimezone()
    {
        if (self::$defaultTimeZone === null) {
            self::$defaultTimeZone = new DateTimeZone('UTC');
        }

        return self::$defaultTimeZone;
    }

    
    private static function validateTimeZone($timeZone)
    {
        if ($timeZone instanceof DateTimeZone) {
            return $timeZone;
        }
        if (in_array($timeZone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC))) {
            return new DateTimeZone($timeZone);
        }

        throw new PhpSpreadsheetException('Invalid timezone');
    }

    
    public static function excelToDateTimeObject($excelTimestamp, $timeZone = null)
    {
        $timeZone = ($timeZone === null) ? self::getDefaultTimezone() : self::validateTimeZone($timeZone);
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            if ($excelTimestamp < 1.0) {
                
                $baseDate = new \DateTime('1970-01-01', $timeZone);
            } else {
                
                if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
                    
                    $baseDate = ($excelTimestamp < 60) ? new \DateTime('1899-12-31', $timeZone) : new \DateTime('1899-12-30', $timeZone);
                } else {
                    $baseDate = new \DateTime('1904-01-01', $timeZone);
                }
            }
        } else {
            $baseDate = new \DateTime('1899-12-30', $timeZone);
        }

        $days = floor($excelTimestamp);
        $partDay = $excelTimestamp - $days;
        $hours = floor($partDay * 24);
        $partDay = $partDay * 24 - $hours;
        $minutes = floor($partDay * 60);
        $partDay = $partDay * 60 - $minutes;
        $seconds = round($partDay * 60);

        if ($days >= 0) {
            $days = '+' . $days;
        }
        $interval = $days . ' days';

        return $baseDate->modify($interval)
            ->setTime((int) $hours, (int) $minutes, (int) $seconds);
    }

    
    public static function excelToTimestamp($excelTimestamp, $timeZone = null)
    {
        return (int) self::excelToDateTimeObject($excelTimestamp, $timeZone)
            ->format('U');
    }

    
    public static function PHPToExcel($dateValue)
    {
        if ((is_object($dateValue)) && ($dateValue instanceof DateTimeInterface)) {
            return self::dateTimeToExcel($dateValue);
        } elseif (is_numeric($dateValue)) {
            return self::timestampToExcel($dateValue);
        } elseif (is_string($dateValue)) {
            return self::stringToExcel($dateValue);
        }

        return false;
    }

    
    public static function dateTimeToExcel(DateTimeInterface $dateValue)
    {
        return self::formattedPHPToExcel(
            (int) $dateValue->format('Y'),
            (int) $dateValue->format('m'),
            (int) $dateValue->format('d'),
            (int) $dateValue->format('H'),
            (int) $dateValue->format('i'),
            (int) $dateValue->format('s')
        );
    }

    
    public static function timestampToExcel($dateValue)
    {
        if (!is_numeric($dateValue)) {
            return false;
        }

        return self::dateTimeToExcel(new \DateTime('@' . $dateValue));
    }

    
    public static function formattedPHPToExcel($year, $month, $day, $hours = 0, $minutes = 0, $seconds = 0)
    {
        if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
            
            
            
            
            $excel1900isLeapYear = true;
            if (($year == 1900) && ($month <= 2)) {
                $excel1900isLeapYear = false;
            }
            $myexcelBaseDate = 2415020;
        } else {
            $myexcelBaseDate = 2416481;
            $excel1900isLeapYear = false;
        }

        
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        
        $century = substr($year, 0, 2);
        $decade = substr($year, 2, 2);
        $excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myexcelBaseDate + $excel1900isLeapYear;

        $excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

        return (float) $excelDate + $excelTime;
    }

    
    public static function isDateTime(Cell $pCell)
    {
        return is_numeric($pCell->getCalculatedValue()) &&
            self::isDateTimeFormat(
                $pCell->getWorksheet()->getStyle(
                    $pCell->getCoordinate()
                )->getNumberFormat()
            );
    }

    
    public static function isDateTimeFormat(NumberFormat $pFormat)
    {
        return self::isDateTimeFormatCode($pFormat->getFormatCode());
    }

    private static $possibleDateFormatCharacters = 'eymdHs';

    
    public static function isDateTimeFormatCode($pFormatCode)
    {
        if (strtolower($pFormatCode) === strtolower(NumberFormat::FORMAT_GENERAL)) {
            
            return false;
        }
        if (preg_match('/[0#]E[+-]0/i', $pFormatCode)) {
            
            return false;
        }

        
        switch ($pFormatCode) {
            
            case NumberFormat::FORMAT_DATE_YYYYMMDD:
            case NumberFormat::FORMAT_DATE_YYYYMMDD2:
            case NumberFormat::FORMAT_DATE_DDMMYYYY:
            case NumberFormat::FORMAT_DATE_DMYSLASH:
            case NumberFormat::FORMAT_DATE_DMYMINUS:
            case NumberFormat::FORMAT_DATE_DMMINUS:
            case NumberFormat::FORMAT_DATE_MYMINUS:
            case NumberFormat::FORMAT_DATE_DATETIME:
            case NumberFormat::FORMAT_DATE_TIME1:
            case NumberFormat::FORMAT_DATE_TIME2:
            case NumberFormat::FORMAT_DATE_TIME3:
            case NumberFormat::FORMAT_DATE_TIME4:
            case NumberFormat::FORMAT_DATE_TIME5:
            case NumberFormat::FORMAT_DATE_TIME6:
            case NumberFormat::FORMAT_DATE_TIME7:
            case NumberFormat::FORMAT_DATE_TIME8:
            case NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
            case NumberFormat::FORMAT_DATE_XLSX14:
            case NumberFormat::FORMAT_DATE_XLSX15:
            case NumberFormat::FORMAT_DATE_XLSX16:
            case NumberFormat::FORMAT_DATE_XLSX17:
            case NumberFormat::FORMAT_DATE_XLSX22:
                return true;
        }

        
        if ((substr($pFormatCode, 0, 1) == '_') || (substr($pFormatCode, 0, 2) == '0 ')) {
            return false;
        }
        
        
        if (\strpos($pFormatCode, '-00000') !== false) {
            return false;
        }
        
        if (preg_match('/(^|\])[^\[]*[' . self::$possibleDateFormatCharacters . ']/i', $pFormatCode)) {
            
            
            if (strpos($pFormatCode, '"') !== false) {
                $segMatcher = false;
                foreach (explode('"', $pFormatCode) as $subVal) {
                    
                    if (
                        ($segMatcher = !$segMatcher) &&
                        (preg_match('/(^|\])[^\[]*[' . self::$possibleDateFormatCharacters . ']/i', $subVal))
                    ) {
                        return true;
                    }
                }

                return false;
            }

            return true;
        }

        
        return false;
    }

    
    public static function stringToExcel($dateValue)
    {
        if (strlen($dateValue) < 2) {
            return false;
        }
        if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue)) {
            return false;
        }

        $dateValueNew = DateTime::DATEVALUE($dateValue);

        if ($dateValueNew === Functions::VALUE()) {
            return false;
        }

        if (strpos($dateValue, ':') !== false) {
            $timeValue = DateTime::TIMEVALUE($dateValue);
            if ($timeValue === Functions::VALUE()) {
                return false;
            }
            $dateValueNew += $timeValue;
        }

        return $dateValueNew;
    }

    
    public static function monthStringToNumber($month)
    {
        $monthIndex = 1;
        foreach (self::$monthNames as $shortMonthName => $longMonthName) {
            if (($month === $longMonthName) || ($month === $shortMonthName)) {
                return $monthIndex;
            }
            ++$monthIndex;
        }

        return $month;
    }

    
    public static function dayStringToNumber($day)
    {
        $strippedDayValue = (str_replace(self::$numberSuffixes, '', $day));
        if (is_numeric($strippedDayValue)) {
            return (int) $strippedDayValue;
        }

        return $day;
    }
}
