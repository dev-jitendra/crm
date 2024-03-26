<?php

namespace Laminas\Crypt\Key\Derivation;

use Laminas\Crypt\Hmac;

use function ceil;
use function hash_hmac;
use function mb_substr;
use function pack;


class Pbkdf2
{
    
    public static function calc($hash, $password, $salt, $iterations, $length)
    {
        if (! Hmac::isSupported($hash)) {
            throw new Exception\InvalidArgumentException("The hash algorithm $hash is not supported by " . self::class);
        }

        $num    = ceil($length / Hmac::getOutputSize($hash, Hmac::OUTPUT_BINARY));
        $result = '';
        for ($block = 1; $block <= $num; $block++) {
            $hmac = hash_hmac($hash, $salt . pack('N', $block), $password, Hmac::OUTPUT_BINARY);
            $mix  = $hmac;
            for ($i = 1; $i < $iterations; $i++) {
                $hmac = hash_hmac($hash, $hmac, $password, Hmac::OUTPUT_BINARY);
                $mix ^= $hmac;
            }
            $result .= $mix;
        }
        return mb_substr($result, 0, $length, '8bit');
    }
}
