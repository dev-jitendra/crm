<?php

namespace Laminas\Math;

use Error;
use TypeError;

use function base64_encode;
use function ceil;
use function chr;
use function mb_strlen;
use function mb_substr;
use function ord;
use function random_bytes;
use function random_int;
use function rtrim;
use function str_repeat;
use function unpack;



abstract class Rand
{
    
    
    protected static $generator;

    
    public static function getBytes($length)
    {
        try {
            return random_bytes($length);
        } catch (TypeError $e) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter provided to getBytes(length)',
                0,
                $e
            );
        } catch (Error $e) {
            throw new Exception\DomainException(
                'The length must be a positive number in getBytes(length)',
                0,
                $e
            );
        }
    }

    
    public static function getBoolean()
    {
        $byte = static::getBytes(1);
        return (bool) (ord($byte) % 2);
    }

    
    public static function getInteger($min, $max)
    {
        try {
            return random_int($min, $max);
        } catch (TypeError $e) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameters provided to getInteger(min, max)',
                0,
                $e
            );
        } catch (Error $e) {
            throw new Exception\DomainException(
                'The min parameter must be lower than max in getInteger(min, max)',
                0,
                $e
            );
        }
    }

    
    public static function getFloat()
    {
        $bytes = static::getBytes(7);
        
        $bytes[6] = $bytes[6] | chr(0xF0);
        $bytes   .= chr(63); 
        $float    = unpack('d', $bytes)[1];

        return $float - 1;
    }

    
    public static function getString($length, $charlist = null)
    {
        if ($length < 1) {
            throw new Exception\DomainException('Length should be >= 1');
        }

        
        if (empty($charlist)) {
            $numBytes = ceil($length * 0.75);
            $bytes    = static::getBytes($numBytes);
            return mb_substr(rtrim(base64_encode($bytes), '='), 0, $length, '8bit');
        }

        $listLen = mb_strlen($charlist, '8bit');

        
        if ($listLen == 1) {
            return str_repeat($charlist, $length);
        }

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $pos     = static::getInteger(0, $listLen - 1);
            $result .= $charlist[$pos];
        }
        return $result;
    }
}
