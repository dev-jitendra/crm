<?php


namespace libphonenumber;

use Giggsey\Locale\Locale;
use libphonenumber\prefixmapper\PrefixFileReader;

class PhoneNumberToCarrierMapper
{
    
    protected static $instance = array();

    
    protected $phoneUtil;
    
    protected $prefixFileReader;

    
    protected function __construct($phonePrefixDataDirectory)
    {
        if ($phonePrefixDataDirectory === null) {
            $phonePrefixDataDirectory = __DIR__ . '/carrier/data/';
        }

        $this->prefixFileReader = new PrefixFileReader($phonePrefixDataDirectory);
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    
    public static function getInstance($mappingDir = null)
    {
        if (!array_key_exists($mappingDir, static::$instance)) {
            static::$instance[$mappingDir] = new static($mappingDir);
        }

        return static::$instance[$mappingDir];
    }

    
    public function getNameForValidNumber(PhoneNumber $number, $languageCode)
    {
        $languageStr = Locale::getPrimaryLanguage($languageCode);
        $scriptStr = '';
        $regionStr = Locale::getRegion($languageCode);

        return $this->prefixFileReader->getDescriptionForNumber($number, $languageStr, $scriptStr, $regionStr);
    }


    
    public function getNameForNumber(PhoneNumber $number, $languageCode)
    {
        $numberType = $this->phoneUtil->getNumberType($number);
        if ($this->isMobile($numberType)) {
            return $this->getNameForValidNumber($number, $languageCode);
        }
        return '';
    }

    
    public function getSafeDisplayName(PhoneNumber $number, $languageCode)
    {
        if ($this->phoneUtil->isMobileNumberPortableRegion($this->phoneUtil->getRegionCodeForNumber($number))) {
            return '';
        }

        return $this->getNameForNumber($number, $languageCode);
    }

    
    protected function isMobile($numberType)
    {
        return ($numberType === PhoneNumberType::MOBILE ||
            $numberType === PhoneNumberType::FIXED_LINE_OR_MOBILE ||
            $numberType === PhoneNumberType::PAGER
        );
    }
}
