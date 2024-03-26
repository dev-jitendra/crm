<?php

namespace libphonenumber\prefixmapper;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;


class PrefixFileReader
{
    protected $phonePrefixDataDirectory;
    
    protected $mappingFileProvider;
    
    protected $availablePhonePrefixMaps = array();

    public function __construct($phonePrefixDataDirectory)
    {
        $this->phonePrefixDataDirectory = $phonePrefixDataDirectory;
        $this->loadMappingFileProvider();
    }

    protected function loadMappingFileProvider()
    {
        $mapPath = $this->phonePrefixDataDirectory . DIRECTORY_SEPARATOR . 'Map.php';
        if (!file_exists($mapPath)) {
            throw new \InvalidArgumentException("Invalid data directory: $mapPath");
        }

        $map = require $mapPath;

        $this->mappingFileProvider = new MappingFileProvider($map);
    }


    
    public function getPhonePrefixDescriptions($prefixMapKey, $language, $script, $region)
    {
        $fileName = $this->mappingFileProvider->getFileName($prefixMapKey, $language, $script, $region);
        if (strlen($fileName) == 0) {
            return null;
        }

        if (!isset($this->availablePhonePrefixMaps[$fileName])) {
            $this->loadPhonePrefixMapFromFile($fileName);
        }

        return $this->availablePhonePrefixMaps[$fileName];
    }

    protected function loadPhonePrefixMapFromFile($fileName)
    {
        $path = $this->phonePrefixDataDirectory . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Data does not exist');
        }

        $map = require $path;
        $areaCodeMap = new PhonePrefixMap($map);

        $this->availablePhonePrefixMaps[$fileName] = $areaCodeMap;
    }

    public function mayFallBackToEnglish($language)
    {
        
        
        
        
        return ($language != 'zh' && $language != 'ja' && $language != 'ko');
    }

    
    public function getDescriptionForNumber(PhoneNumber $number, $language, $script, $region)
    {
        $phonePrefix = $number->getCountryCode() . PhoneNumberUtil::getInstance()->getNationalSignificantNumber($number);

        $phonePrefixDescriptions = $this->getPhonePrefixDescriptions($phonePrefix, $language, $script, $region);

        $description = ($phonePrefixDescriptions !== null) ? $phonePrefixDescriptions->lookup($number) : null;
        
        if (($description === null || strlen($description) === 0) && $this->mayFallBackToEnglish($language)) {
            $defaultMap = $this->getPhonePrefixDescriptions($phonePrefix, 'en', '', '');
            if ($defaultMap === null) {
                return '';
            }
            $description = $defaultMap->lookup($number);
        }

        return ($description !== null) ? $description : '';
    }
}
