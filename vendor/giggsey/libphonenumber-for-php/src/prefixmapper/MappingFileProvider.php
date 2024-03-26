<?php

namespace libphonenumber\prefixmapper;


class MappingFileProvider
{
    protected $map;

    public function __construct($map)
    {
        $this->map = $map;
    }

    public function getFileName($countryCallingCode, $language, $script, $region)
    {
        if (strlen($language) == 0) {
            return '';
        }

        if ($language === 'zh' && ($region == 'TW' || $region == 'HK' || $region == 'MO')) {
            $language = 'zh_Hant';
        }

        
        $prefixLength = strlen($countryCallingCode);

        for ($i = $prefixLength; $i > 0; $i--) {
            $prefix = substr($countryCallingCode, 0, $i);
            if ($this->inMap($language, $prefix)) {
                return $language . DIRECTORY_SEPARATOR . $prefix . '.php';
            }
        }

        return '';
    }

    protected function inMap($language, $countryCallingCode)
    {
        return (array_key_exists($language, $this->map) && in_array($countryCallingCode, $this->map[$language]));
    }
}
