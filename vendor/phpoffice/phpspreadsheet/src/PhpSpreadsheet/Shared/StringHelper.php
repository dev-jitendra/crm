<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class StringHelper
{
    
    
    
    const STRING_REGEXP_FRACTION = '(-?)(\d+)\s+(\d+\/\d+)';

    
    private static $controlCharacters = [];

    
    private static $SYLKCharacters = [];

    
    private static $decimalSeparator;

    
    private static $thousandsSeparator;

    
    private static $currencyCode;

    
    private static $isIconvEnabled;

    
    private static $iconvOptions = '

    
    private static function buildControlCharacters(): void
    {
        for ($i = 0; $i <= 31; ++$i) {
            if ($i != 9 && $i != 10 && $i != 13) {
                $find = '_x' . sprintf('%04s', strtoupper(dechex($i))) . '_';
                $replace = chr($i);
                self::$controlCharacters[$find] = $replace;
            }
        }
    }

    
    private static function buildSYLKCharacters(): void
    {
        self::$SYLKCharacters = [
            "\x1B 0" => chr(0),
            "\x1B 1" => chr(1),
            "\x1B 2" => chr(2),
            "\x1B 3" => chr(3),
            "\x1B 4" => chr(4),
            "\x1B 5" => chr(5),
            "\x1B 6" => chr(6),
            "\x1B 7" => chr(7),
            "\x1B 8" => chr(8),
            "\x1B 9" => chr(9),
            "\x1B :" => chr(10),
            "\x1B ;" => chr(11),
            "\x1B <" => chr(12),
            "\x1B =" => chr(13),
            "\x1B >" => chr(14),
            "\x1B ?" => chr(15),
            "\x1B!0" => chr(16),
            "\x1B!1" => chr(17),
            "\x1B!2" => chr(18),
            "\x1B!3" => chr(19),
            "\x1B!4" => chr(20),
            "\x1B!5" => chr(21),
            "\x1B!6" => chr(22),
            "\x1B!7" => chr(23),
            "\x1B!8" => chr(24),
            "\x1B!9" => chr(25),
            "\x1B!:" => chr(26),
            "\x1B!;" => chr(27),
            "\x1B!<" => chr(28),
            "\x1B!=" => chr(29),
            "\x1B!>" => chr(30),
            "\x1B!?" => chr(31),
            "\x1B'?" => chr(127),
            "\x1B(0" => '€', 
            "\x1B(2" => '‚', 
            "\x1B(3" => 'ƒ', 
            "\x1B(4" => '„', 
            "\x1B(5" => '…', 
            "\x1B(6" => '†', 
            "\x1B(7" => '‡', 
            "\x1B(8" => 'ˆ', 
            "\x1B(9" => '‰', 
            "\x1B(:" => 'Š', 
            "\x1B(;" => '‹', 
            "\x1BNj" => 'Œ', 
            "\x1B(>" => 'Ž', 
            "\x1B)1" => '‘', 
            "\x1B)2" => '’', 
            "\x1B)3" => '“', 
            "\x1B)4" => '”', 
            "\x1B)5" => '•', 
            "\x1B)6" => '–', 
            "\x1B)7" => '—', 
            "\x1B)8" => '˜', 
            "\x1B)9" => '™', 
            "\x1B):" => 'š', 
            "\x1B);" => '›', 
            "\x1BNz" => 'œ', 
            "\x1B)>" => 'ž', 
            "\x1B)?" => 'Ÿ', 
            "\x1B*0" => ' ', 
            "\x1BN!" => '¡', 
            "\x1BN\"" => '¢', 
            "\x1BN#" => '£', 
            "\x1BN(" => '¤', 
            "\x1BN%" => '¥', 
            "\x1B*6" => '¦', 
            "\x1BN'" => '§', 
            "\x1BNH " => '¨', 
            "\x1BNS" => '©', 
            "\x1BNc" => 'ª', 
            "\x1BN+" => '«', 
            "\x1B*<" => '¬', 
            "\x1B*=" => '­', 
            "\x1BNR" => '®', 
            "\x1B*?" => '¯', 
            "\x1BN0" => '°', 
            "\x1BN1" => '±', 
            "\x1BN2" => '²', 
            "\x1BN3" => '³', 
            "\x1BNB " => '´', 
            "\x1BN5" => 'µ', 
            "\x1BN6" => '¶', 
            "\x1BN7" => '·', 
            "\x1B+8" => '¸', 
            "\x1BNQ" => '¹', 
            "\x1BNk" => 'º', 
            "\x1BN;" => '»', 
            "\x1BN<" => '¼', 
            "\x1BN=" => '½', 
            "\x1BN>" => '¾', 
            "\x1BN?" => '¿', 
            "\x1BNAA" => 'À', 
            "\x1BNBA" => 'Á', 
            "\x1BNCA" => 'Â', 
            "\x1BNDA" => 'Ã', 
            "\x1BNHA" => 'Ä', 
            "\x1BNJA" => 'Å', 
            "\x1BNa" => 'Æ', 
            "\x1BNKC" => 'Ç', 
            "\x1BNAE" => 'È', 
            "\x1BNBE" => 'É', 
            "\x1BNCE" => 'Ê', 
            "\x1BNHE" => 'Ë', 
            "\x1BNAI" => 'Ì', 
            "\x1BNBI" => 'Í', 
            "\x1BNCI" => 'Î', 
            "\x1BNHI" => 'Ï', 
            "\x1BNb" => 'Ð', 
            "\x1BNDN" => 'Ñ', 
            "\x1BNAO" => 'Ò', 
            "\x1BNBO" => 'Ó', 
            "\x1BNCO" => 'Ô', 
            "\x1BNDO" => 'Õ', 
            "\x1BNHO" => 'Ö', 
            "\x1B-7" => '×', 
            "\x1BNi" => 'Ø', 
            "\x1BNAU" => 'Ù', 
            "\x1BNBU" => 'Ú', 
            "\x1BNCU" => 'Û', 
            "\x1BNHU" => 'Ü', 
            "\x1B-=" => 'Ý', 
            "\x1BNl" => 'Þ', 
            "\x1BN{" => 'ß', 
            "\x1BNAa" => 'à', 
            "\x1BNBa" => 'á', 
            "\x1BNCa" => 'â', 
            "\x1BNDa" => 'ã', 
            "\x1BNHa" => 'ä', 
            "\x1BNJa" => 'å', 
            "\x1BNq" => 'æ', 
            "\x1BNKc" => 'ç', 
            "\x1BNAe" => 'è', 
            "\x1BNBe" => 'é', 
            "\x1BNCe" => 'ê', 
            "\x1BNHe" => 'ë', 
            "\x1BNAi" => 'ì', 
            "\x1BNBi" => 'í', 
            "\x1BNCi" => 'î', 
            "\x1BNHi" => 'ï', 
            "\x1BNs" => 'ð', 
            "\x1BNDn" => 'ñ', 
            "\x1BNAo" => 'ò', 
            "\x1BNBo" => 'ó', 
            "\x1BNCo" => 'ô', 
            "\x1BNDo" => 'õ', 
            "\x1BNHo" => 'ö', 
            "\x1B/7" => '÷', 
            "\x1BNy" => 'ø', 
            "\x1BNAu" => 'ù', 
            "\x1BNBu" => 'ú', 
            "\x1BNCu" => 'û', 
            "\x1BNHu" => 'ü', 
            "\x1B/=" => 'ý', 
            "\x1BN|" => 'þ', 
            "\x1BNHy" => 'ÿ', 
        ];
    }

    
    public static function getIsIconvEnabled()
    {
        if (isset(self::$isIconvEnabled)) {
            return self::$isIconvEnabled;
        }

        
        self::$isIconvEnabled = true;

        
        if (!function_exists('iconv')) {
            self::$isIconvEnabled = false;
        } elseif (!@iconv('UTF-8', 'UTF-16LE', 'x')) {
            
            self::$isIconvEnabled = false;
        } elseif (defined('PHP_OS') && @stristr(PHP_OS, 'AIX') && defined('ICONV_IMPL') && (@strcasecmp(ICONV_IMPL, 'unknown') == 0) && defined('ICONV_VERSION') && (@strcasecmp(ICONV_VERSION, 'unknown') == 0)) {
            
            self::$isIconvEnabled = false;
        }

        
        if (self::$isIconvEnabled && !@iconv('UTF-8', 'UTF-16LE' . self::$iconvOptions, 'x')) {
            self::$iconvOptions = '';
        }

        return self::$isIconvEnabled;
    }

    private static function buildCharacterSets(): void
    {
        if (empty(self::$controlCharacters)) {
            self::buildControlCharacters();
        }

        if (empty(self::$SYLKCharacters)) {
            self::buildSYLKCharacters();
        }
    }

    
    public static function controlCharacterOOXML2PHP($value)
    {
        self::buildCharacterSets();

        return str_replace(array_keys(self::$controlCharacters), array_values(self::$controlCharacters), $value);
    }

    
    public static function controlCharacterPHP2OOXML($value)
    {
        self::buildCharacterSets();

        return str_replace(array_values(self::$controlCharacters), array_keys(self::$controlCharacters), $value);
    }

    
    public static function sanitizeUTF8($value)
    {
        if (self::getIsIconvEnabled()) {
            $value = @iconv('UTF-8', 'UTF-8', $value);

            return $value;
        }

        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

        return $value;
    }

    
    public static function isUTF8($value)
    {
        return $value === '' || preg_match('/^./su', $value) === 1;
    }

    
    public static function formatNumber($value)
    {
        if (is_float($value)) {
            return str_replace(',', '.', $value);
        }

        return (string) $value;
    }

    
    public static function UTF8toBIFF8UnicodeShort($value, $arrcRuns = [])
    {
        
        $ln = self::countCharacters($value, 'UTF-8');
        
        if (empty($arrcRuns)) {
            $data = pack('CC', $ln, 0x0001);
            
            $data .= self::convertEncoding($value, 'UTF-16LE', 'UTF-8');
        } else {
            $data = pack('vC', $ln, 0x09);
            $data .= pack('v', count($arrcRuns));
            
            $data .= self::convertEncoding($value, 'UTF-16LE', 'UTF-8');
            foreach ($arrcRuns as $cRun) {
                $data .= pack('v', $cRun['strlen']);
                $data .= pack('v', $cRun['fontidx']);
            }
        }

        return $data;
    }

    
    public static function UTF8toBIFF8UnicodeLong($value)
    {
        
        $ln = self::countCharacters($value, 'UTF-8');

        
        $chars = self::convertEncoding($value, 'UTF-16LE', 'UTF-8');

        return pack('vC', $ln, 0x0001) . $chars;
    }

    
    public static function convertEncoding($value, $to, $from)
    {
        if (self::getIsIconvEnabled()) {
            $result = iconv($from, $to . self::$iconvOptions, $value);
            if (false !== $result) {
                return $result;
            }
        }

        return mb_convert_encoding($value, $to, $from);
    }

    
    public static function countCharacters($value, $enc = 'UTF-8')
    {
        return mb_strlen($value, $enc);
    }

    
    public static function substring($pValue, $pStart, $pLength = 0)
    {
        return mb_substr($pValue, $pStart, $pLength, 'UTF-8');
    }

    
    public static function strToUpper($pValue)
    {
        return mb_convert_case($pValue, MB_CASE_UPPER, 'UTF-8');
    }

    
    public static function strToLower($pValue)
    {
        return mb_convert_case($pValue, MB_CASE_LOWER, 'UTF-8');
    }

    
    public static function strToTitle($pValue)
    {
        return mb_convert_case($pValue, MB_CASE_TITLE, 'UTF-8');
    }

    public static function mbIsUpper($char)
    {
        return mb_strtolower($char, 'UTF-8') != $char;
    }

    public static function mbStrSplit($string)
    {
        
        
        return preg_split('/(?<!^)(?!$)/u', $string);
    }

    
    public static function strCaseReverse($pValue)
    {
        $characters = self::mbStrSplit($pValue);
        foreach ($characters as &$character) {
            if (self::mbIsUpper($character)) {
                $character = mb_strtolower($character, 'UTF-8');
            } else {
                $character = mb_strtoupper($character, 'UTF-8');
            }
        }

        return implode('', $characters);
    }

    
    public static function convertToNumberIfFraction(&$operand)
    {
        if (preg_match('/^' . self::STRING_REGEXP_FRACTION . '$/i', $operand, $match)) {
            $sign = ($match[1] == '-') ? '-' : '+';
            $fractionFormula = '=' . $sign . $match[2] . $sign . $match[3];
            $operand = Calculation::getInstance()->_calculateFormulaValue($fractionFormula);

            return true;
        }

        return false;
    }

    

    
    public static function getDecimalSeparator()
    {
        if (!isset(self::$decimalSeparator)) {
            $localeconv = localeconv();
            self::$decimalSeparator = ($localeconv['decimal_point'] != '')
                ? $localeconv['decimal_point'] : $localeconv['mon_decimal_point'];

            if (self::$decimalSeparator == '') {
                
                self::$decimalSeparator = '.';
            }
        }

        return self::$decimalSeparator;
    }

    
    public static function setDecimalSeparator($pValue): void
    {
        self::$decimalSeparator = $pValue;
    }

    
    public static function getThousandsSeparator()
    {
        if (!isset(self::$thousandsSeparator)) {
            $localeconv = localeconv();
            self::$thousandsSeparator = ($localeconv['thousands_sep'] != '')
                ? $localeconv['thousands_sep'] : $localeconv['mon_thousands_sep'];

            if (self::$thousandsSeparator == '') {
                
                self::$thousandsSeparator = ',';
            }
        }

        return self::$thousandsSeparator;
    }

    
    public static function setThousandsSeparator($pValue): void
    {
        self::$thousandsSeparator = $pValue;
    }

    
    public static function getCurrencyCode()
    {
        if (!empty(self::$currencyCode)) {
            return self::$currencyCode;
        }
        self::$currencyCode = '$';
        $localeconv = localeconv();
        if (!empty($localeconv['currency_symbol'])) {
            self::$currencyCode = $localeconv['currency_symbol'];

            return self::$currencyCode;
        }
        if (!empty($localeconv['int_curr_symbol'])) {
            self::$currencyCode = $localeconv['int_curr_symbol'];

            return self::$currencyCode;
        }

        return self::$currencyCode;
    }

    
    public static function setCurrencyCode($pValue): void
    {
        self::$currencyCode = $pValue;
    }

    
    public static function SYLKtoUTF8($pValue)
    {
        self::buildCharacterSets();

        
        if (strpos($pValue, '') === false) {
            return $pValue;
        }

        foreach (self::$SYLKCharacters as $k => $v) {
            $pValue = str_replace($k, $v, $pValue);
        }

        return $pValue;
    }

    
    public static function testStringAsNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        $v = (float) $value;

        return (is_numeric(substr($value, 0, strlen($v)))) ? $v : $value;
    }
}
