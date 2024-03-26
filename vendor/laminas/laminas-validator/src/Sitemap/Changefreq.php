<?php

namespace Laminas\Validator\Sitemap;

use Laminas\Validator\AbstractValidator;

use function in_array;
use function is_string;


class Changefreq extends AbstractValidator
{
    
    public const NOT_VALID = 'sitemapChangefreqNotValid';
    public const INVALID   = 'sitemapChangefreqInvalid';

    
    protected $messageTemplates = [
        self::NOT_VALID => 'The input is not a valid sitemap changefreq',
        self::INVALID   => 'Invalid type given. String expected',
    ];

    
    protected $changeFreqs = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    ];

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (! in_array($value, $this->changeFreqs, true)) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}
