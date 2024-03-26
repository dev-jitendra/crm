<?php


namespace Espo\Core\Authentication\Jwt;

use Espo\Core\Authentication\Jwt\Exceptions\UnsupportedKey;
use Espo\Core\Authentication\Jwt\Keys\Rsa;
use stdClass;

class DefaultKeyFactory implements KeyFactory
{
    private const TYPE_RSA = 'RSA';

    public function create(stdClass $raw): Key
    {
        $kty = $raw->kty ?? null;

        if ($kty === self::TYPE_RSA) {
            return Rsa::fromRaw($raw);
        }

        throw new UnsupportedKey();
    }
}
