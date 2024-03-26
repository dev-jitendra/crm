<?php


namespace Espo\Core\Utils;

use JsonException;

use const JSON_THROW_ON_ERROR;

class Json
{
    
    public static function encode($value, int $options = 0): string
    {
        return json_encode($value, $options | JSON_THROW_ON_ERROR);
    }

    
    public static function decode(string $json, bool $associative = false)
    {
        return json_decode($json, $associative, 512, JSON_THROW_ON_ERROR);
    }
}
