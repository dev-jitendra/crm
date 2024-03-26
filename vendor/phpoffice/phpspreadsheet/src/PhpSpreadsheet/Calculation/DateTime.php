<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use DateTimeImmutable;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DateTime
{
    
    public static function isLeapYear($year)
    {
        return (($year % 4) === 0) && (($year % 100) !== 0) || (($year % 400) === 0);
    }

    
    private static function dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $methodUS)
    {
        if ($startDay == 31) {
            --$startDay;
        } elseif ($methodUS && ($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !self::isLeapYear($startYear))))) {
            $startDay = 30;
        }
        if ($endDay == 31) {
            if ($methodUS && $startDay != 30) {
                $endDay = 1;
                if ($endMonth == 12) {
                    ++$endYear;
                    $endMonth = 1;
                } else {
                    ++$endMonth;
                }
            } else {
                $endDay = 30;
            }
        }

        return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
    }

    
    public static function getDateValue($dateValue)
    {
        if (!is_numeric($dateValue)) {
            if ((is_object($dateValue)) && ($dateValue instanceof DateTimeInterface)) {
                $dateValue = Date::PHPToExcel($dateValue);
            } else {
                $saveReturnDateType = Functions::getReturnDateType();
                Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
                $dateValue = self::DATEVALUE($dateValue);
                Functions::setReturnDateType($saveReturnDateType);
            }
        }

        return $dateValue;
    }

    
    private static function getTimeValue($timeValue)
    {
        $saveReturnDateType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $timeValue = self::TIMEVALUE($timeValue);
        Functions::setReturnDateType($saveReturnDateType);

        return $timeValue;
    }

    private static function adjustDateByMonths($dateValue = 0, $adjustmentMonths = 0)
    {
        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        $oMonth = (int) $PHPDateObject->format('m');
        $oYear = (int) $PHPDateObject->format('Y');

        $adjustmentMonthsString = (string) $adjustmentMonths;
        if ($adjustmentMonths > 0) {
            $adjustmentMonthsString = '+' . $adjustmentMonths;
        }
        if ($adjustmentMonths != 0) {
            $PHPDateObject->modify($adjustmentMonthsString . ' months');
        }
        $nMonth = (int) $PHPDateObject->format('m');
        $nYear = (int) $PHPDateObject->format('Y');

        $monthDiff = ($nMonth - $oMonth) + (($nYear - $oYear) * 12);
        if ($monthDiff != $adjustmentMonths) {
            $adjustDays = (int) $PHPDateObject->format('d');
            $adjustDaysString = '-' . $adjustDays . ' days';
            $PHPDateObject->modify($adjustDaysString);
        }

        return $PHPDateObject;
    }

    
    public static function DATETIMENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $retValue = (float) Date::PHPToExcel(time());

                break;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                $retValue = (int) time();

                break;
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $retValue = new \DateTime();

                break;
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }

    
    public static function DATENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        $excelDateTime = floor(Date::PHPToExcel(time()));
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $retValue = (float) $excelDateTime;

                break;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                $retValue = (int) Date::excelToTimestamp($excelDateTime);

                break;
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $retValue = Date::excelToDateTimeObject($excelDateTime);

                break;
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }

    
    public static function DATE($year = 0, $month = 1, $day = 1)
    {
        $year = Functions::flattenSingleValue($year);
        $month = Functions::flattenSingleValue($month);
        $day = Functions::flattenSingleValue($day);

        if (($month !== null) && (!is_numeric($month))) {
            $month = Date::monthStringToNumber($month);
        }

        if (($day !== null) && (!is_numeric($day))) {
            $day = Date::dayStringToNumber($day);
        }

        $year = ($year !== null) ? StringHelper::testStringAsNumeric($year) : 0;
        $month = ($month !== null) ? StringHelper::testStringAsNumeric($month) : 0;
        $day = ($day !== null) ? StringHelper::testStringAsNumeric($day) : 0;
        if (
            (!is_numeric($year)) ||
            (!is_numeric($month)) ||
            (!is_numeric($day))
        ) {
            return Functions::VALUE();
        }
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;

        $baseYear = Date::getExcelCalendar();
        
        if ($year < ($baseYear - 1900)) {
            return Functions::NAN();
        }
        if ((($baseYear - 1900) != 0) && ($year < $baseYear) && ($year >= 1900)) {
            return Functions::NAN();
        }

        if (($year < $baseYear) && ($year >= ($baseYear - 1900))) {
            $year += 1900;
        }

        if ($month < 1) {
            
            --$month;
            $year += ceil($month / 12) - 1;
            $month = 13 - abs($month % 12);
        } elseif ($month > 12) {
            
            $year += floor($month / 12);
            $month = ($month % 12);
        }

        
        if (($year < $baseYear) || ($year >= 10000)) {
            return Functions::NAN();
        }

        
        $excelDateValue = Date::formattedPHPToExcel($year, $month, $day);
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) $excelDateValue;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp($excelDateValue);
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return Date::excelToDateTimeObject($excelDateValue);
        }
    }

    
    public static function TIME($hour = 0, $minute = 0, $second = 0)
    {
        $hour = Functions::flattenSingleValue($hour);
        $minute = Functions::flattenSingleValue($minute);
        $second = Functions::flattenSingleValue($second);

        if ($hour == '') {
            $hour = 0;
        }
        if ($minute == '') {
            $minute = 0;
        }
        if ($second == '') {
            $second = 0;
        }

        if ((!is_numeric($hour)) || (!is_numeric($minute)) || (!is_numeric($second))) {
            return Functions::VALUE();
        }
        $hour = (int) $hour;
        $minute = (int) $minute;
        $second = (int) $second;

        if ($second < 0) {
            $minute += floor($second / 60);
            $second = 60 - abs($second % 60);
            if ($second == 60) {
                $second = 0;
            }
        } elseif ($second >= 60) {
            $minute += floor($second / 60);
            $second = $second % 60;
        }
        if ($minute < 0) {
            $hour += floor($minute / 60);
            $minute = 60 - abs($minute % 60);
            if ($minute == 60) {
                $minute = 0;
            }
        } elseif ($minute >= 60) {
            $hour += floor($minute / 60);
            $minute = $minute % 60;
        }

        if ($hour > 23) {
            $hour = $hour % 24;
        } elseif ($hour < 0) {
            return Functions::NAN();
        }

        
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $date = 0;
                $calendar = Date::getExcelCalendar();
                if ($calendar != Date::CALENDAR_WINDOWS_1900) {
                    $date = 1;
                }

                return (float) Date::formattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::formattedPHPToExcel(1970, 1, 1, $hour, $minute, $second)); 
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $dayAdjust = 0;
                if ($hour < 0) {
                    $dayAdjust = floor($hour / 24);
                    $hour = 24 - abs($hour % 24);
                    if ($hour == 24) {
                        $hour = 0;
                    }
                } elseif ($hour >= 24) {
                    $dayAdjust = floor($hour / 24);
                    $hour = $hour % 24;
                }
                $phpDateObject = new \DateTime('1900-01-01 ' . $hour . ':' . $minute . ':' . $second);
                if ($dayAdjust != 0) {
                    $phpDateObject->modify($dayAdjust . ' days');
                }

                return $phpDateObject;
        }
    }

    
    public static function DATEVALUE($dateValue = 1)
    {
        $dateValue = trim(Functions::flattenSingleValue($dateValue), '"');
        
        $dateValue = preg_replace('/(\d)(st|nd|rd|th)([ -\/])/Ui', '$1$3', $dateValue);
        
        $dateValue = str_replace(['/', '.', '-', '  '], ' ', $dateValue);

        $yearFound = false;
        $t1 = explode(' ', $dateValue);
        foreach ($t1 as &$t) {
            if ((is_numeric($t)) && ($t > 31)) {
                if ($yearFound) {
                    return Functions::VALUE();
                }
                if ($t < 100) {
                    $t += 1900;
                }
                $yearFound = true;
            }
        }
        if ((count($t1) == 1) && (strpos($t, ':') !== false)) {
            
            return 0.0;
        } elseif (count($t1) == 2) {
            
            if ($yearFound) {
                array_unshift($t1, 1);
            } else {
                if (is_numeric($t1[1]) && $t1[1] > 29) {
                    $t1[1] += 1900;
                    array_unshift($t1, 1);
                } else {
                    $t1[] = date('Y');
                }
            }
        }
        unset($t);
        $dateValue = implode(' ', $t1);

        $PHPDateArray = date_parse($dateValue);
        if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
            $testVal1 = strtok($dateValue, '- ');
            if ($testVal1 !== false) {
                $testVal2 = strtok('- ');
                if ($testVal2 !== false) {
                    $testVal3 = strtok('- ');
                    if ($testVal3 === false) {
                        $testVal3 = strftime('%Y');
                    }
                } else {
                    return Functions::VALUE();
                }
            } else {
                return Functions::VALUE();
            }
            if ($testVal1 < 31 && $testVal2 < 12 && $testVal3 < 12 && strlen($testVal3) == 2) {
                $testVal3 += 2000;
            }
            $PHPDateArray = date_parse($testVal1 . '-' . $testVal2 . '-' . $testVal3);
            if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
                $PHPDateArray = date_parse($testVal2 . '-' . $testVal1 . '-' . $testVal3);
                if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
                    return Functions::VALUE();
                }
            }
        }

        if (($PHPDateArray !== false) && ($PHPDateArray['error_count'] == 0)) {
            
            if ($PHPDateArray['year'] == '') {
                $PHPDateArray['year'] = strftime('%Y');
            }
            if ($PHPDateArray['year'] < 1900) {
                return Functions::VALUE();
            }
            if ($PHPDateArray['month'] == '') {
                $PHPDateArray['month'] = strftime('%m');
            }
            if ($PHPDateArray['day'] == '') {
                $PHPDateArray['day'] = strftime('%d');
            }
            if (!checkdate($PHPDateArray['month'], $PHPDateArray['day'], $PHPDateArray['year'])) {
                return Functions::VALUE();
            }
            $excelDateValue = floor(
                Date::formattedPHPToExcel(
                    $PHPDateArray['year'],
                    $PHPDateArray['month'],
                    $PHPDateArray['day'],
                    $PHPDateArray['hour'],
                    $PHPDateArray['minute'],
                    $PHPDateArray['second']
                )
            );
            switch (Functions::getReturnDateType()) {
                case Functions::RETURNDATE_EXCEL:
                    return (float) $excelDateValue;
                case Functions::RETURNDATE_UNIX_TIMESTAMP:
                    return (int) Date::excelToTimestamp($excelDateValue);
                case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                    return new \DateTime($PHPDateArray['year'] . '-' . $PHPDateArray['month'] . '-' . $PHPDateArray['day'] . ' 00:00:00');
            }
        }

        return Functions::VALUE();
    }

    
    public static function TIMEVALUE($timeValue)
    {
        $timeValue = trim(Functions::flattenSingleValue($timeValue), '"');
        $timeValue = str_replace(['/', '.'], '-', $timeValue);

        $arraySplit = preg_split('/[\/:\-\s]/', $timeValue);
        if ((count($arraySplit) == 2 || count($arraySplit) == 3) && $arraySplit[0] > 24) {
            $arraySplit[0] = ($arraySplit[0] % 24);
            $timeValue = implode(':', $arraySplit);
        }

        $PHPDateArray = date_parse($timeValue);
        if (($PHPDateArray !== false) && ($PHPDateArray['error_count'] == 0)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $excelDateValue = Date::formattedPHPToExcel(
                    $PHPDateArray['year'],
                    $PHPDateArray['month'],
                    $PHPDateArray['day'],
                    $PHPDateArray['hour'],
                    $PHPDateArray['minute'],
                    $PHPDateArray['second']
                );
            } else {
                $excelDateValue = Date::formattedPHPToExcel(1900, 1, 1, $PHPDateArray['hour'], $PHPDateArray['minute'], $PHPDateArray['second']) - 1;
            }

            switch (Functions::getReturnDateType()) {
                case Functions::RETURNDATE_EXCEL:
                    return (float) $excelDateValue;
                case Functions::RETURNDATE_UNIX_TIMESTAMP:
                    return (int) $phpDateValue = Date::excelToTimestamp($excelDateValue + 25569) - 3600;
                case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                    return new \DateTime('1900-01-01 ' . $PHPDateArray['hour'] . ':' . $PHPDateArray['minute'] . ':' . $PHPDateArray['second']);
            }
        }

        return Functions::VALUE();
    }

    
    public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D')
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        $unit = strtoupper(Functions::flattenSingleValue($unit));

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }

        
        if ($startDate > $endDate) {
            return Functions::NAN();
        }

        
        $difference = $endDate - $startDate;

        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $startDays = $PHPStartDateObject->format('j');
        $startMonths = $PHPStartDateObject->format('n');
        $startYears = $PHPStartDateObject->format('Y');

        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);
        $endDays = $PHPEndDateObject->format('j');
        $endMonths = $PHPEndDateObject->format('n');
        $endYears = $PHPEndDateObject->format('Y');

        $PHPDiffDateObject = $PHPEndDateObject->diff($PHPStartDateObject);

        switch ($unit) {
            case 'D':
                $retVal = (int) $difference;

                break;
            case 'M':
                $retVal = (int) 12 * $PHPDiffDateObject->format('%y') + $PHPDiffDateObject->format('%m');

                break;
            case 'Y':
                $retVal = (int) $PHPDiffDateObject->format('%y');

                break;
            case 'MD':
                if ($endDays < $startDays) {
                    $retVal = $endDays;
                    $PHPEndDateObject->modify('-' . $endDays . ' days');
                    $adjustDays = $PHPEndDateObject->format('j');
                    $retVal += ($adjustDays - $startDays);
                } else {
                    $retVal = (int) $PHPDiffDateObject->format('%d');
                }

                break;
            case 'YM':
                $retVal = (int) $PHPDiffDateObject->format('%m');

                break;
            case 'YD':
                $retVal = (int) $difference;
                if ($endYears > $startYears) {
                    $isLeapStartYear = $PHPStartDateObject->format('L');
                    $wasLeapEndYear = $PHPEndDateObject->format('L');

                    
                    while ($PHPEndDateObject >= $PHPStartDateObject) {
                        $PHPEndDateObject->modify('-1 year');
                        $endYears = $PHPEndDateObject->format('Y');
                    }
                    $PHPEndDateObject->modify('+1 year');

                    
                    $retVal = $PHPEndDateObject->diff($PHPStartDateObject)->days;

                    
                    $isLeapEndYear = $PHPEndDateObject->format('L');
                    $limit = new \DateTime($PHPEndDateObject->format('Y-02-29'));
                    if (!$isLeapStartYear && !$wasLeapEndYear && $isLeapEndYear && $PHPEndDateObject >= $limit) {
                        --$retVal;
                    }
                }

                break;
            default:
                $retVal = Functions::VALUE();
        }

        return $retVal;
    }

    
    public static function DAYS($endDate = 0, $startDate = 0)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);

        $startDate = self::getDateValue($startDate);
        if (is_string($startDate)) {
            return Functions::VALUE();
        }

        $endDate = self::getDateValue($endDate);
        if (is_string($endDate)) {
            return Functions::VALUE();
        }

        
        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);

        $diff = $PHPStartDateObject->diff($PHPEndDateObject);
        $days = $diff->days;

        if ($diff->invert) {
            $days = -$days;
        }

        return $days;
    }

    
    public static function DAYS360($startDate = 0, $endDate = 0, $method = false)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }

        if (!is_bool($method)) {
            return Functions::VALUE();
        }

        
        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $startDay = $PHPStartDateObject->format('j');
        $startMonth = $PHPStartDateObject->format('n');
        $startYear = $PHPStartDateObject->format('Y');

        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);
        $endDay = $PHPEndDateObject->format('j');
        $endMonth = $PHPEndDateObject->format('n');
        $endYear = $PHPEndDateObject->format('Y');

        return self::dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, !$method);
    }

    
    public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        $method = Functions::flattenSingleValue($method);

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        if (((is_numeric($method)) && (!is_string($method))) || ($method == '')) {
            switch ($method) {
                case 0:
                    return self::DAYS360($startDate, $endDate) / 360;
                case 1:
                    $days = self::DATEDIF($startDate, $endDate);
                    $startYear = self::YEAR($startDate);
                    $endYear = self::YEAR($endDate);
                    $years = $endYear - $startYear + 1;
                    $startMonth = self::MONTHOFYEAR($startDate);
                    $startDay = self::DAYOFMONTH($startDate);
                    $endMonth = self::MONTHOFYEAR($endDate);
                    $endDay = self::DAYOFMONTH($endDate);
                    $startMonthDay = 100 * $startMonth + $startDay;
                    $endMonthDay = 100 * $endMonth + $endDay;
                    if ($years == 1) {
                        if (self::isLeapYear($endYear)) {
                            $tmpCalcAnnualBasis = 366;
                        } else {
                            $tmpCalcAnnualBasis = 365;
                        }
                    } elseif ($years == 2 && $startMonthDay >= $endMonthDay) {
                        if (self::isLeapYear($startYear)) {
                            if ($startMonthDay <= 229) {
                                $tmpCalcAnnualBasis = 366;
                            } else {
                                $tmpCalcAnnualBasis = 365;
                            }
                        } elseif (self::isLeapYear($endYear)) {
                            if ($endMonthDay >= 229) {
                                $tmpCalcAnnualBasis = 366;
                            } else {
                                $tmpCalcAnnualBasis = 365;
                            }
                        } else {
                            $tmpCalcAnnualBasis = 365;
                        }
                    } else {
                        $tmpCalcAnnualBasis = 0;
                        for ($year = $startYear; $year <= $endYear; ++$year) {
                            $tmpCalcAnnualBasis += self::isLeapYear($year) ? 366 : 365;
                        }
                        $tmpCalcAnnualBasis /= $years;
                    }

                    return $days / $tmpCalcAnnualBasis;
                case 2:
                    return self::DATEDIF($startDate, $endDate) / 360;
                case 3:
                    return self::DATEDIF($startDate, $endDate) / 365;
                case 4:
                    return self::DAYS360($startDate, $endDate, true) / 360;
            }
        }

        return Functions::VALUE();
    }

    
    public static function NETWORKDAYS($startDate, $endDate, ...$dateArgs)
    {
        
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        
        $dateArgs = Functions::flattenArray($dateArgs);

        
        if (is_string($startDate = $sDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        $startDate = (float) floor($startDate);
        if (is_string($endDate = $eDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }
        $endDate = (float) floor($endDate);

        if ($sDate > $eDate) {
            $startDate = $eDate;
            $endDate = $sDate;
        }

        
        $startDoW = 6 - self::WEEKDAY($startDate, 2);
        if ($startDoW < 0) {
            $startDoW = 0;
        }
        $endDoW = self::WEEKDAY($endDate, 2);
        if ($endDoW >= 6) {
            $endDoW = 0;
        }

        $wholeWeekDays = floor(($endDate - $startDate) / 7) * 5;
        $partWeekDays = $endDoW + $startDoW;
        if ($partWeekDays > 5) {
            $partWeekDays -= 5;
        }

        
        $holidayCountedArray = [];
        foreach ($dateArgs as $holidayDate) {
            if (is_string($holidayDate = self::getDateValue($holidayDate))) {
                return Functions::VALUE();
            }
            if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                if ((self::WEEKDAY($holidayDate, 2) < 6) && (!in_array($holidayDate, $holidayCountedArray))) {
                    --$partWeekDays;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
        }

        if ($sDate > $eDate) {
            return 0 - ($wholeWeekDays + $partWeekDays);
        }

        return $wholeWeekDays + $partWeekDays;
    }

    
    public static function WORKDAY($startDate, $endDays, ...$dateArgs)
    {
        
        $startDate = Functions::flattenSingleValue($startDate);
        $endDays = Functions::flattenSingleValue($endDays);
        
        $dateArgs = Functions::flattenArray($dateArgs);

        if ((is_string($startDate = self::getDateValue($startDate))) || (!is_numeric($endDays))) {
            return Functions::VALUE();
        }
        $startDate = (float) floor($startDate);
        $endDays = (int) floor($endDays);
        
        if ($endDays == 0) {
            return $startDate;
        }

        $decrementing = $endDays < 0;

        

        $startDoW = self::WEEKDAY($startDate, 3);
        if (self::WEEKDAY($startDate, 3) >= 5) {
            $startDate += ($decrementing) ? -$startDoW + 4 : 7 - $startDoW;
            ($decrementing) ? $endDays++ : $endDays--;
        }

        
        $endDate = (float) $startDate + ((int) ($endDays / 5) * 7) + ($endDays % 5);

        
        $endDoW = self::WEEKDAY($endDate, 3);
        if ($endDoW >= 5) {
            $endDate += ($decrementing) ? -$endDoW + 4 : 7 - $endDoW;
        }

        
        if (!empty($dateArgs)) {
            $holidayCountedArray = $holidayDates = [];
            foreach ($dateArgs as $holidayDate) {
                if (($holidayDate !== null) && (trim($holidayDate) > '')) {
                    if (is_string($holidayDate = self::getDateValue($holidayDate))) {
                        return Functions::VALUE();
                    }
                    if (self::WEEKDAY($holidayDate, 3) < 5) {
                        $holidayDates[] = $holidayDate;
                    }
                }
            }
            if ($decrementing) {
                rsort($holidayDates, SORT_NUMERIC);
            } else {
                sort($holidayDates, SORT_NUMERIC);
            }
            foreach ($holidayDates as $holidayDate) {
                if ($decrementing) {
                    if (($holidayDate <= $startDate) && ($holidayDate >= $endDate)) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            --$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                } else {
                    if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            ++$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                }
                
                $endDoW = self::WEEKDAY($endDate, 3);
                if ($endDoW >= 5) {
                    $endDate += ($decrementing) ? -$endDoW + 4 : 7 - $endDoW;
                }
            }
        }

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) $endDate;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp($endDate);
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return Date::excelToDateTimeObject($endDate);
        }
    }

    
    public static function DAYOFMONTH($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            if ($dateValue < 0.0) {
                return Functions::NAN();
            } elseif ($dateValue < 1.0) {
                return 0;
            }
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('j');
    }

    
    public static function WEEKDAY($dateValue = 1, $style = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $style = Functions::flattenSingleValue($style);

        if (!is_numeric($style)) {
            return Functions::VALUE();
        } elseif (($style < 1) || ($style > 3)) {
            return Functions::NAN();
        }
        $style = floor($style);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        $DoW = (int) $PHPDateObject->format('w');

        $firstDay = 1;
        switch ($style) {
            case 1:
                ++$DoW;

                break;
            case 2:
                if ($DoW === 0) {
                    $DoW = 7;
                }

                break;
            case 3:
                if ($DoW === 0) {
                    $DoW = 7;
                }
                $firstDay = 0;
                --$DoW;

                break;
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            
            if (($PHPDateObject->format('Y') == 1900) && ($PHPDateObject->format('n') <= 2)) {
                --$DoW;
                if ($DoW < $firstDay) {
                    $DoW += 7;
                }
            }
        }

        return $DoW;
    }

    const STARTWEEK_SUNDAY = 1;
    const STARTWEEK_MONDAY = 2;
    const STARTWEEK_MONDAY_ALT = 11;
    const STARTWEEK_TUESDAY = 12;
    const STARTWEEK_WEDNESDAY = 13;
    const STARTWEEK_THURSDAY = 14;
    const STARTWEEK_FRIDAY = 15;
    const STARTWEEK_SATURDAY = 16;
    const STARTWEEK_SUNDAY_ALT = 17;
    const DOW_SUNDAY = 1;
    const DOW_MONDAY = 2;
    const DOW_TUESDAY = 3;
    const DOW_WEDNESDAY = 4;
    const DOW_THURSDAY = 5;
    const DOW_FRIDAY = 6;
    const DOW_SATURDAY = 7;
    const STARTWEEK_MONDAY_ISO = 21;
    const METHODARR = [
        self::STARTWEEK_SUNDAY => self::DOW_SUNDAY,
        self::DOW_MONDAY,
        self::STARTWEEK_MONDAY_ALT => self::DOW_MONDAY,
        self::DOW_TUESDAY,
        self::DOW_WEDNESDAY,
        self::DOW_THURSDAY,
        self::DOW_FRIDAY,
        self::DOW_SATURDAY,
        self::DOW_SUNDAY,
        self::STARTWEEK_MONDAY_ISO => self::STARTWEEK_MONDAY_ISO,
    ];

    
    public static function WEEKNUM($dateValue = 1, $method = self::STARTWEEK_SUNDAY)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $method = Functions::flattenSingleValue($method);

        if (!is_numeric($method)) {
            return Functions::VALUE();
        }
        $method = (int) $method;
        if (!array_key_exists($method, self::METHODARR)) {
            return Functions::NaN();
        }
        $method = self::METHODARR[$method];

        $dateValue = self::getDateValue($dateValue);
        if (is_string($dateValue)) {
            return Functions::VALUE();
        }
        if ($dateValue < 0.0) {
            return Functions::NAN();
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        if ($method == self::STARTWEEK_MONDAY_ISO) {
            return (int) $PHPDateObject->format('W');
        }
        $dayOfYear = $PHPDateObject->format('z');
        $PHPDateObject->modify('-' . $dayOfYear . ' days');
        $firstDayOfFirstWeek = $PHPDateObject->format('w');
        $daysInFirstWeek = (6 - $firstDayOfFirstWeek + $method) % 7;
        $daysInFirstWeek += 7 * !$daysInFirstWeek;
        $endFirstWeek = $daysInFirstWeek - 1;
        $weekOfYear = floor(($dayOfYear - $endFirstWeek + 13) / 7);

        return (int) $weekOfYear;
    }

    
    public static function ISOWEEKNUM($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('W');
    }

    
    public static function MONTHOFYEAR($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if (empty($dateValue)) {
            $dateValue = 1;
        }
        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('n');
    }

    
    public static function YEAR($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('Y');
    }

    
    public static function HOUROFDAY($timeValue = 0)
    {
        $timeValue = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('G', $timeValue);
    }

    
    public static function MINUTE($timeValue = 0)
    {
        $timeValue = $timeTester = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('i', $timeValue);
    }

    
    public static function SECOND($timeValue = 0)
    {
        $timeValue = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('s', $timeValue);
    }

    
    public static function EDATE($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = Functions::flattenSingleValue($adjustmentMonths);

        if (!is_numeric($adjustmentMonths)) {
            return Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        
        $PHPDateObject = self::adjustDateByMonths($dateValue, $adjustmentMonths);

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) Date::PHPToExcel($PHPDateObject);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::PHPToExcel($PHPDateObject));
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return $PHPDateObject;
        }
    }

    
    public static function EOMONTH($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = Functions::flattenSingleValue($adjustmentMonths);

        if (!is_numeric($adjustmentMonths)) {
            return Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        
        $PHPDateObject = self::adjustDateByMonths($dateValue, $adjustmentMonths + 1);
        $adjustDays = (int) $PHPDateObject->format('d');
        $adjustDaysString = '-' . $adjustDays . ' days';
        $PHPDateObject->modify($adjustDaysString);

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) Date::PHPToExcel($PHPDateObject);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::PHPToExcel($PHPDateObject));
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return $PHPDateObject;
        }
    }
}
