<?php

namespace Laminas\Mail\Header;

use function in_array;
use function strlen;


class ListParser
{
    public const CHAR_QUOTES = ['\'', '"'];
    public const CHAR_DELIMS = [',', ';'];
    public const CHAR_ESCAPE = '\\';

    
    public static function parse($value, array $delims = self::CHAR_DELIMS)
    {
        $values            = [];
        $length            = strlen($value);
        $currentValue      = '';
        $inEscape          = false;
        $inQuote           = false;
        $currentQuoteDelim = null;

        for ($i = 0; $i < $length; $i += 1) {
            $char = $value[$i];

            
            if ($inEscape) {
                $currentValue .= $char;
                $inEscape      = false;
                continue;
            }

            
            
            if (in_array($char, $delims, true) && ! $inQuote) {
                $values []    = $currentValue;
                $currentValue = '';
                continue;
            }

            
            $currentValue .= $char;

            
            if (self::CHAR_ESCAPE === $char) {
                $inEscape = true;
                continue;
            }

            
            
            if (! in_array($char, self::CHAR_QUOTES)) {
                continue;
            }

            
            
            
            if ($char === $currentQuoteDelim) {
                $inQuote           = false;
                $currentQuoteDelim = null;
                continue;
            }

            
            
            if ($inQuote) {
                continue;
            }

            
            $inQuote           = true;
            $currentQuoteDelim = $char;
        }

        
        
        if ('' !== $currentValue) {
            $values [] = $currentValue;
        }

        return $values;
    }
}
