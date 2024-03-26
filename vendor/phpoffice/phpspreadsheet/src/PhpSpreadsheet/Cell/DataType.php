<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DataType
{
    
    const TYPE_STRING2 = 'str';
    const TYPE_STRING = 's';
    const TYPE_FORMULA = 'f';
    const TYPE_NUMERIC = 'n';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'null';
    const TYPE_INLINE = 'inlineStr';
    const TYPE_ERROR = 'e';

    
    private static $errorCodes = [
        '#NULL!' => 0,
        '#DIV/0!' => 1,
        '#VALUE!' => 2,
        '#REF!' => 3,
        '#NAME?' => 4,
        '#NUM!' => 5,
        '#N/A' => 6,
    ];

    
    public static function getErrorCodes()
    {
        return self::$errorCodes;
    }

    
    public static function checkString($pValue)
    {
        if ($pValue instanceof RichText) {
            
            return $pValue;
        }

        
        $pValue = StringHelper::substring($pValue, 0, 32767);

        
        $pValue = str_replace(["\r\n", "\r"], "\n", $pValue);

        return $pValue;
    }

    
    public static function checkErrorCode($pValue)
    {
        $pValue = (string) $pValue;

        if (!isset(self::$errorCodes[$pValue])) {
            $pValue = '#NULL!';
        }

        return $pValue;
    }
}
