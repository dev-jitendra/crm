<?php

namespace Laminas\Validator\Sitemap;

use Laminas\Validator\AbstractValidator;

use function is_numeric;


class Priority extends AbstractValidator
{
    
    public const NOT_VALID = 'sitemapPriorityNotValid';
    public const INVALID   = 'sitemapPriorityInvalid';

    
    protected $messageTemplates = [
        self::NOT_VALID => 'The input is not a valid sitemap priority',
        self::INVALID   => 'Invalid type given. Numeric string, integer or float expected',
    ];

    
    public function isValid($value)
    {
        if (! is_numeric($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $value = (float) $value;
        if ($value < 0 || $value > 1) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}
