<?php

namespace Laminas\Validator\Sitemap;

use Laminas\Stdlib\ErrorHandler;
use Laminas\Validator\AbstractValidator;

use function is_string;
use function preg_match;


class Lastmod extends AbstractValidator
{
    

    
    public const LASTMOD_REGEX = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])(T([0-1][0-9]|2[0-3])(:[0-5][0-9])(:[0-5][0-9])?(\\+|-)([0-1][0-9]|2[0-3]):[0-5][0-9])?$/';

    

    
    public const NOT_VALID = 'sitemapLastmodNotValid';
    public const INVALID   = 'sitemapLastmodInvalid';

    
    protected $messageTemplates = [
        self::NOT_VALID => 'The input is not a valid sitemap lastmod',
        self::INVALID   => 'Invalid type given. String expected',
    ];

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        ErrorHandler::start();
        $result = preg_match(self::LASTMOD_REGEX, $value);
        ErrorHandler::stop();
        if ($result !== 1) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}
