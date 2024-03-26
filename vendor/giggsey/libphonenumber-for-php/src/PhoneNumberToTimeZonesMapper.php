<?php


namespace libphonenumber;

use libphonenumber\prefixmapper\PrefixTimeZonesMap;

class PhoneNumberToTimeZonesMapper
{
    const UNKNOWN_TIMEZONE = 'Etc/Unknown';
    const MAPPING_DATA_DIRECTORY = '/timezone/data/';
    const MAPPING_DATA_FILE_NAME = 'map_data.php';
    
    protected static $instance;
    protected $unknownTimeZoneList = array();
    
    protected $phoneUtil;
    protected $prefixTimeZonesMap;

    protected function __construct($phonePrefixDataDirectory)
    {
        $this->prefixTimeZonesMap = static::loadPrefixTimeZonesMapFromFile(
            \dirname(__FILE__) . $phonePrefixDataDirectory . DIRECTORY_SEPARATOR . static::MAPPING_DATA_FILE_NAME
        );
        $this->phoneUtil = PhoneNumberUtil::getInstance();

        $this->unknownTimeZoneList[] = static::UNKNOWN_TIMEZONE;
    }

    protected static function loadPrefixTimeZonesMapFromFile($path)
    {
        if (!\is_readable($path)) {
            throw new \InvalidArgumentException('Mapping file can not be found');
        }

        $data = require $path;

        $map = new PrefixTimeZonesMap($data);

        return $map;
    }

    
    public static function getInstance($mappingDir = self::MAPPING_DATA_DIRECTORY)
    {
        if (static::$instance === null) {
            static::$instance = new static($mappingDir);
        }

        return static::$instance;
    }

    
    public static function getUnknownTimeZone()
    {
        return static::UNKNOWN_TIMEZONE;
    }

    
    public function getTimeZonesForNumber(PhoneNumber $number)
    {
        $numberType = $this->phoneUtil->getNumberType($number);

        if ($numberType === PhoneNumberType::UNKNOWN) {
            return $this->unknownTimeZoneList;
        }

        if (!PhoneNumberUtil::getInstance()->isNumberGeographical($numberType, $number->getCountryCode())) {
            return $this->getCountryLevelTimeZonesforNumber($number);
        }

        return $this->getTimeZonesForGeographicalNumber($number);
    }

    
    protected function getCountryLevelTimeZonesforNumber(PhoneNumber $number)
    {
        $timezones = $this->prefixTimeZonesMap->lookupCountryLevelTimeZonesForNumber($number);
        return (\count($timezones) == 0) ? $this->unknownTimeZoneList : $timezones;
    }

    
    public function getTimeZonesForGeographicalNumber(PhoneNumber $number)
    {
        return $this->getTimeZonesForGeocodableNumber($number);
    }

    
    protected function getTimeZonesForGeocodableNumber(PhoneNumber $number)
    {
        $timezones = $this->prefixTimeZonesMap->lookupTimeZonesForNumber($number);
        return (\count($timezones) == 0) ? $this->unknownTimeZoneList : $timezones;
    }
}
