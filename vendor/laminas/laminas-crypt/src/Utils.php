<?php

namespace Laminas\Crypt;

use function function_exists;
use function hash_equals;
use function mb_strlen;
use function min;
use function ord;


class Utils
{
    
    public static function compareStrings($expected, $actual)
    {
        $expected = (string) $expected;
        $actual   = (string) $actual;

        if (function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }

        $lenExpected = mb_strlen($expected, '8bit');
        $lenActual   = mb_strlen($actual, '8bit');
        $len         = min($lenExpected, $lenActual);

        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;

        return $result === 0;
    }
}
