<?php

declare(strict_types=1);

namespace Brick\PhoneNumber;


final class PhoneNumberParseException extends PhoneNumberException
{
    
    public static function wrap(\Exception $e) : PhoneNumberParseException
    {
        return new PhoneNumberParseException($e->getMessage(), $e->getCode(), $e);
    }
}
