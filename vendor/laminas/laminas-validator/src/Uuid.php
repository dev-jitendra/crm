<?php

namespace Laminas\Validator;

use function is_string;
use function preg_match;


final class Uuid extends AbstractValidator
{
    
    public const REGEX_UUID = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

    public const INVALID    = 'valueNotUuid';
    public const NOT_STRING = 'valueNotString';

    
    protected $messageTemplates = [
        self::NOT_STRING => 'Invalid type given; string expected',
        self::INVALID    => 'Invalid UUID format',
    ];

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::NOT_STRING);
            return false;
        }

        $this->setValue($value);

        if (
            empty($value)
            || $value !== '00000000-0000-0000-0000-000000000000'
            && ! preg_match(self::REGEX_UUID, $value)
        ) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
