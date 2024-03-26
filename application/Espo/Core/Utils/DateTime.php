<?php


namespace Espo\Core\Utils;

use Carbon\Carbon;

use Espo\Core\Field\Date;
use Espo\Core\Field\DateTime as DateTimeField;

use DateTime as DateTimeStd;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use RuntimeException;



class DateTime
{
    public const SYSTEM_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const SYSTEM_DATE_FORMAT = 'Y-m-d';

    private string $dateFormat;
    private string $timeFormat;
    private DateTimeZone $timezone;
    private string $language;

    public function __construct(
        ?string $dateFormat = 'YYYY-MM-DD',
        ?string $timeFormat = 'HH:mm',
        ?string $timeZone = 'UTC',
        ?string $language = 'en_US'
    ) {
        $this->dateFormat = $dateFormat ?? 'YYYY-MM-DD';
        $this->timeFormat = $timeFormat ?? 'HH:mm';
        $this->language = $language ?? 'en_US';

        try {
            $this->timezone = new DateTimeZone($timeZone ?? 'UTC');
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    
    public function getDateTimeFormat(): string
    {
        return $this->dateFormat . ' ' . $this->timeFormat;
    }

    
    public function convertSystemDate(
        string $string,
        ?string $format = null,
        ?string $language = null
    ): string {

        $dateTime = DateTimeStd::createFromFormat('Y-m-d', $string);

        if ($dateTime === false) {
            throw new RuntimeException("Could not parse date `$string`.");
        }

        $carbon = Carbon::instance($dateTime);

        $carbon->locale($language ?? $this->language);

        return $carbon->isoFormat($format ?? $this->getDateFormat());
    }

    
    public function convertSystemDateTime(
        string $string,
        ?string $timezone = null,
        ?string $format = null,
        ?string $language = null
    ): string {

        if (strlen($string) === 16) {
            $string .= ':00';
        }

        $dateTime = DateTimeStd::createFromFormat('Y-m-d H:i:s', $string);

        if ($dateTime === false) {
            throw new RuntimeException("Could not parse date-time `$string`.");
        }

        try {
            $tz = $timezone ? new DateTimeZone($timezone) : $this->timezone;
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $dateTime->setTimezone($tz);

        $carbon = Carbon::instance($dateTime);
        $carbon->locale($language ?? $this->language);

        return $carbon->isoFormat($format ?? $this->getDateTimeFormat());
    }

    
    public function getTodayString(?string $timezone = null, ?string $format = null): string
    {
        try {
            $tz = $timezone ? new DateTimeZone($timezone) : $this->timezone;
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $dateTime = new DateTimeStd();
        $dateTime->setTimezone($tz);

        $carbon = Carbon::instance($dateTime);
        $carbon->locale($this->language);

        return $carbon->isoFormat($format ?? $this->getDateFormat());
    }

    
    public function getNowString(?string $timezone = null, ?string $format = null): string
    {
        try {
            $tz = $timezone ? new DateTimeZone($timezone) : $this->timezone;
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $dateTime = new DateTimeStd();

        $dateTime->setTimezone($tz);

        $carbon = Carbon::instance($dateTime);

        $carbon->locale($this->language);

        return $carbon->isoFormat($format ?? $this->getDateTimeFormat());
    }

    
    public static function getSystemNowString(): string
    {
        return date(self::SYSTEM_DATE_TIME_FORMAT);
    }

    public static function getSystemTodayString(): string
    {
        return date(self::SYSTEM_DATE_FORMAT);
    }

    
    public static function convertFormatToSystem(string $format): string
    {
        $map = [
            'MM' => 'm',
            'DD' => 'd',
            'YYYY' => 'Y',
            'HH' => 'H',
            'mm' => 'i',
            'hh' => 'h',
            'A' => 'A',
            'a' => 'a',
            'ss' => 's',
        ];

        return str_replace(
            array_keys($map),
            array_values($map),
            $format
        );
    }

    
    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    
    public function getToday(): Date
    {
        $string = (new DateTimeImmutable)
            ->setTimezone($this->timezone)
            ->format(self::SYSTEM_DATE_FORMAT);

        return Date::fromString($string);
    }

    
    public function getNow(): DateTimeField
    {
        return DateTimeField::createNow()
            ->withTimezone($this->timezone);
    }

    
    public function getInternalDateTimeFormat(): string
    {
        return self::SYSTEM_DATE_TIME_FORMAT;
    }

    
    public function getInternalDateFormat(): string
    {
        return self::SYSTEM_DATE_FORMAT;
    }

    
    public function convertSystemDateToGlobal($string): string
    {
        return $this->convertSystemDate($string);
    }

    
    public function convertSystemDateTimeToGlobal(string $string): string
    {
        return $this->convertSystemDateTime($string);
    }
}
