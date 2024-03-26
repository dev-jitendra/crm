<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Helper;


final class DateFormatHelper
{
    public const KEY_GENERAL = 'general';
    public const KEY_HOUR_12 = '12h';
    public const KEY_HOUR_24 = '24h';

    
    private const excelDateFormatToPHPDateFormatMapping = [
        self::KEY_GENERAL => [
            
            'am/pm' => 'A',  
            ':mm' => ':i', 
            'mm:' => 'i:', 
            'ss' => 's',  
            '.s' => '',   

            
            'e' => 'Y',  
            'yyyy' => 'Y',  
            'yy' => 'y',  
            'mmmmm' => 'M',  
            'mmmm' => 'F',  
            'mmm' => 'M',  
            'mm' => 'm',  
            'm' => 'n',  
            'dddd' => 'l',  
            'ddd' => 'D',  
            'dd' => 'd',  
            'd' => 'j',  
        ],
        self::KEY_HOUR_12 => [
            'hh' => 'h',  
            'h' => 'g',  
        ],
        self::KEY_HOUR_24 => [
            'hh' => 'H',  
            'h' => 'G',  
        ],
    ];

    
    public static function toPHPDateFormat(string $excelDateFormat): string
    {
        
        
        
        $dateFormat = preg_replace('/^(?:\[\$[^\]]+?\])?([^;]*).*/', '$1', $excelDateFormat);
        \assert(null !== $dateFormat);

        
        
        
        
        $dateFormatParts = explode('"', $dateFormat);

        foreach ($dateFormatParts as $partIndex => $dateFormatPart) {
            
            if (1 === $partIndex % 2) {
                continue;
            }

            
            $transformedPart = strtolower($dateFormatPart);

            
            $transformedPart = str_replace('\\', '', $transformedPart);

            
            $transformedPart = strtr($transformedPart, self::excelDateFormatToPHPDateFormatMapping[self::KEY_GENERAL]);

            
            if (self::has12HourFormatMarker($dateFormatPart)) {
                $transformedPart = strtr($transformedPart, self::excelDateFormatToPHPDateFormatMapping[self::KEY_HOUR_12]);
            } else {
                $transformedPart = strtr($transformedPart, self::excelDateFormatToPHPDateFormatMapping[self::KEY_HOUR_24]);
            }

            
            $dateFormatParts[$partIndex] = $transformedPart;
        }

        
        $phpDateFormat = implode('"', $dateFormatParts);

        
        
        
        return preg_replace_callback('/"(.+?)"/', static function ($matches): string {
            $stringToEscape = $matches[1];
            $letters = preg_split('
            \assert(false !== $letters);

            return '\\'.implode('\\', $letters);
        }, $phpDateFormat);
    }

    
    private static function has12HourFormatMarker(string $excelDateFormat): bool
    {
        return false !== stripos($excelDateFormat, 'am/pm');
    }
}
