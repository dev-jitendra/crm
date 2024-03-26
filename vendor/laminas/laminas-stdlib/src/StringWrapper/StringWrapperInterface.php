<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use const STR_PAD_RIGHT;

interface StringWrapperInterface
{
    
    public static function isSupported($encoding, $convertEncoding = null);

    
    public static function getSupportedEncodings();

    
    public function setEncoding($encoding, $convertEncoding = null);

    
    public function getEncoding();

    
    public function getConvertEncoding();

    
    public function strlen($str);

    
    public function substr($str, $offset = 0, $length = null);

    
    public function strpos($haystack, $needle, $offset = 0);

    
    public function convert($str, $reverse = false);

    
    public function wordWrap($str, $width = 75, $break = "\n", $cut = false);

    
    public function strPad($input, $padLength, $padString = ' ', $padType = STR_PAD_RIGHT);
}
