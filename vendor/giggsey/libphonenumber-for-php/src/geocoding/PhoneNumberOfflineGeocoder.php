<?php

namespace libphonenumber\geocoding;

use Giggsey\Locale\Locale;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\prefixmapper\PrefixFileReader;

class PhoneNumberOfflineGeocoder
{
    
    protected static $instance;
    
    protected $phoneUtil;
    
    protected $prefixFileReader;

    
    protected function __construct($phonePrefixDataDirectory)
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();

        if ($phonePrefixDataDirectory === null) {
            $phonePrefixDataDirectory = __DIR__ . '/data/';
        }

        $this->prefixFileReader = new PrefixFileReader($phonePrefixDataDirectory);
    }

    
    public static function getInstance($mappingDir = null)
    {
        if (static::$instance === null) {
            static::$instance = new static($mappingDir);
        }

        return static::$instance;
    }

    public static function resetInstance()
    {
        static::$instance = null;
    }

    
    public function getDescriptionForNumber(PhoneNumber $number, $locale, $userRegion = null)
    {
        $numberType = $this->phoneUtil->getNumberType($number);

        if ($numberType === PhoneNumberType::UNKNOWN) {
            return '';
        }

        if (!$this->phoneUtil->isNumberGeographical($numberType, $number->getCountryCode())) {
            return $this->getCountryNameForNumber($number, $locale);
        }

        return $this->getDescriptionForValidNumber($number, $locale, $userRegion);
    }

    
    protected function getCountryNameForNumber(PhoneNumber $number, $locale)
    {
        $regionCodes = $this->phoneUtil->getRegionCodesForCountryCode($number->getCountryCode());

        if (\count($regionCodes) === 1) {
            return $this->getRegionDisplayName($regionCodes[0], $locale);
        }

        $regionWhereNumberIsValid = 'ZZ';
        foreach ($regionCodes as $regionCode) {
            if ($this->phoneUtil->isValidNumberForRegion($number, $regionCode)) {
                
                
                if ($regionWhereNumberIsValid !== 'ZZ') {
                    return '';
                }
                $regionWhereNumberIsValid = $regionCode;
            }
        }

        return $this->getRegionDisplayName($regionWhereNumberIsValid, $locale);
    }

    
    protected function getRegionDisplayName($regionCode, $locale)
    {
        if ($regionCode === null || $regionCode == 'ZZ' || $regionCode === PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY) {
            return '';
        }

        return Locale::getDisplayRegion(
            '-' . $regionCode,
            $locale
        );
    }

    
    public function getDescriptionForValidNumber(PhoneNumber $number, $locale, $userRegion = null)
    {
        
        
        
        $regionCode = $this->phoneUtil->getRegionCodeForNumber($number);
        if ($userRegion == null || $userRegion == $regionCode) {
            $languageStr = Locale::getPrimaryLanguage($locale);
            $scriptStr = '';
            $regionStr = Locale::getRegion($locale);

            $mobileToken = PhoneNumberUtil::getCountryMobileToken($number->getCountryCode());
            $nationalNumber = $this->phoneUtil->getNationalSignificantNumber($number);
            if ($mobileToken !== '' && (!\strncmp($nationalNumber, $mobileToken, \strlen($mobileToken)))) {
                
                
                $nationalNumber = \substr($nationalNumber, \strlen($mobileToken));
                $region = $this->phoneUtil->getRegionCodeForCountryCode($number->getCountryCode());
                try {
                    $copiedNumber = $this->phoneUtil->parse($nationalNumber, $region);
                } catch (NumberParseException $e) {
                    
                    $copiedNumber = $number;
                }
                $areaDescription = $this->prefixFileReader->getDescriptionForNumber($copiedNumber, $languageStr, $scriptStr, $regionStr);
            } else {
                $areaDescription = $this->prefixFileReader->getDescriptionForNumber($number, $languageStr, $scriptStr, $regionStr);
            }

            return (\strlen($areaDescription) > 0) ? $areaDescription : $this->getCountryNameForNumber($number, $locale);
        }
        
        return $this->getRegionDisplayName($regionCode, $locale);
        
        
    }
}
