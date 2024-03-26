<?php

namespace Giggsey\Locale;

class Locale
{
    protected static $dataDir = '../data/';

    
    public static function getPrimaryLanguage(string $locale): string
    {
        $parts = explode('-', str_replace('_', '-', $locale));

        return strtolower($parts[0]);
    }

    
    public static function getRegion(string $locale): string
    {
        $parts = explode('-', str_replace('_', '-', $locale));

        if (count($parts) === 1) {
            return '';
        }

        $region = end($parts);

        if (strlen($region) === 4) {
            return '';
        }

        if ($region === 'POSIX') {
            $region = 'US';
        }

        return strtoupper($region);
    }

    
    public static function getDisplayRegion(string $locale, string $inLocale): string
    {
        $dataDir = __DIR__ . DIRECTORY_SEPARATOR . static::$dataDir;

        
        $region = static::getRegion($locale);

        $regionList = require $dataDir . '_list.php';

        
        $fallbackParts = explode('-', str_replace('_', '-', $inLocale));
        $filesToSearch = [];

        $i = count($fallbackParts);
        while ($i > 0) {
            $searchLocale = strtolower(implode('-', $fallbackParts));

            if (isset($regionList[$searchLocale])) {
                $filesToSearch[] = $searchLocale;
            }

            array_pop($fallbackParts);
            $i--;
        }

        

        foreach ($filesToSearch as $fileToSearch) {
            
            $data = require $dataDir . $fileToSearch . '.php';

            if (isset($data[$region])) {
                return $data[$region];
            }
        }

        return '';
    }

    public static function getVersion()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . static::$dataDir . '_version.php';

        return require $file;
    }

    
    public static function getSupportedLocales(): array
    {
        $dataDir = __DIR__ . DIRECTORY_SEPARATOR . static::$dataDir;
        $regionList = require $dataDir . '_list.php';

        return array_keys($regionList);
    }

    
    public static function getAllCountriesForLocale(string $locale): array
    {
        $dataDir = __DIR__ . DIRECTORY_SEPARATOR . static::$dataDir;
        $regionList = require $dataDir . '_list.php';

        if (!isset($regionList[$locale])) {
            throw new \RuntimeException("Locale is not supported");
        }

        
        $fallbackParts = explode('-', str_replace('_', '-', $locale));
        $filesToSearch = [];

        $i = count($fallbackParts);
        while ($i > 0) {
            $searchLocale = strtolower(implode('-', $fallbackParts));

            if (isset($regionList[$searchLocale])) {
                $filesToSearch[] = $searchLocale;
            }

            array_pop($fallbackParts);
            $i--;
        }

        

        $returnData = [];

        foreach ($filesToSearch as $fileToSearch) {
            
            $data = require $dataDir . $fileToSearch . '.php';

            $returnData += $data;
        }

        ksort($returnData);

        return $returnData;
    }
}
