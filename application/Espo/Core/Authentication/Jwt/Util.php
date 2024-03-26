<?php


namespace Espo\Core\Authentication\Jwt;

use RuntimeException;

class Util
{
    public static function base64UrlDecode(string $string): string
    {
        $extra = 4 - strlen($string) % 4;
        $extra = $extra < 4 ? $extra : 0;

        $preparedString = strtr($string . str_repeat('=', $extra), '-_', '+/');

        $decoded = base64_decode($preparedString, true);

        if ($decoded === false) {
            throw new RuntimeException("Base64url decoding error.");
        }

        return $decoded;
    }
}
