<?php

namespace Laminas\Validator\Sitemap;

use Laminas\Uri;
use Laminas\Validator\AbstractValidator;

use function is_string;


class Loc extends AbstractValidator
{
    
    public const NOT_VALID = 'sitemapLocNotValid';
    public const INVALID   = 'sitemapLocInvalid';

    
    protected $messageTemplates = [
        self::NOT_VALID => 'The input is not a valid sitemap location',
        self::INVALID   => 'Invalid type given. String expected',
    ];

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $uri = Uri\UriFactory::factory($value);
        if (! $uri->isValid()) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}
