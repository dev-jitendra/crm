<?php

declare(strict_types=1);

namespace Laminas\Mail\Protocol\Xoauth2;

use function base64_encode;
use function chr;
use function sprintf;


final class Xoauth2
{
    
    public static function encodeXoauth2Sasl(string $targetMailbox, string $accessToken): string
    {
        return base64_encode(
            sprintf(
                "user=%s%sauth=Bearer %s%s%s",
                $targetMailbox,
                chr(0x01),
                $accessToken,
                chr(0x01),
                chr(0x01)
            )
        );
    }
}
