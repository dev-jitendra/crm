<?php

namespace Laminas\Validator;

use function ctype_xdigit;
use function is_int;
use function is_string;

class Hex extends AbstractValidator
{
    public const INVALID = 'hexInvalid';
    public const NOT_HEX = 'notHex';

    
    protected $messageTemplates = [
        self::INVALID => 'Invalid type given. String expected',
        self::NOT_HEX => 'The input contains non-hexadecimal characters',
    ];

    
    public function isValid($value)
    {
        if (! is_string($value) && ! is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        if (! ctype_xdigit((string) $value)) {
            $this->error(self::NOT_HEX);
            return false;
        }

        return true;
    }
}
