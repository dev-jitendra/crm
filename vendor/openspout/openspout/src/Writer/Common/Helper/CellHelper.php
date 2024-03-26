<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;


final class CellHelper
{
    
    private static array $columnIndexToColumnLettersCache = [];

    
    public static function getColumnLettersFromColumnIndex(int $columnIndexZeroBased): string
    {
        $originalColumnIndex = $columnIndexZeroBased;

        
        if (!isset(self::$columnIndexToColumnLettersCache[$originalColumnIndex])) {
            $columnLetters = '';
            $capitalAAsciiValue = \ord('A');

            do {
                $modulus = $columnIndexZeroBased % 26;
                $columnLetters = \chr($capitalAAsciiValue + $modulus).$columnLetters;

                
                $columnIndexZeroBased = (int) ($columnIndexZeroBased / 26) - 1;
            } while ($columnIndexZeroBased >= 0);

            self::$columnIndexToColumnLettersCache[$originalColumnIndex] = $columnLetters;
        }

        return self::$columnIndexToColumnLettersCache[$originalColumnIndex];
    }
}
