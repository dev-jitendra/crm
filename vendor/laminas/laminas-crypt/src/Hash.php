<?php

namespace Laminas\Crypt;

use function hash;
use function hash_algos;
use function in_array;
use function mb_strlen;
use function strtolower;

class Hash
{
    public const OUTPUT_STRING = false;
    public const OUTPUT_BINARY = true;

    
    protected static $lastAlgorithmSupported;

    
    public static function compute($hash, $data, $output = self::OUTPUT_STRING)
    {
        if (! $hash || ($hash !== static::$lastAlgorithmSupported && ! static::isSupported($hash))) {
            throw new Exception\InvalidArgumentException(
                'Hash algorithm provided is not supported on this PHP installation'
            );
        }

        return hash($hash, $data, $output);
    }

    
    public static function getOutputSize($hash, $output = self::OUTPUT_STRING)
    {
        return mb_strlen(static::compute($hash, 'data', $output), '8bit');
    }

    
    public static function getSupportedAlgorithms()
    {
        return hash_algos();
    }

    
    public static function isSupported($algorithm)
    {
        if ($algorithm === static::$lastAlgorithmSupported) {
            return true;
        }

        if (in_array(strtolower($algorithm), hash_algos(), true)) {
            static::$lastAlgorithmSupported = $algorithm;
            return true;
        }

        return false;
    }

    
    public static function clearLastAlgorithmCache()
    {
        static::$lastAlgorithmSupported = null;
    }
}
