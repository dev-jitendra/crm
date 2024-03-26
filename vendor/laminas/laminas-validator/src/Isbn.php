<?php

namespace Laminas\Validator;

use function in_array;
use function is_int;
use function is_string;
use function preg_match;
use function quotemeta;
use function str_replace;
use function strlen;
use function substr;

class Isbn extends AbstractValidator
{
    public const AUTO    = 'auto';
    public const ISBN10  = '10';
    public const ISBN13  = '13';
    public const INVALID = 'isbnInvalid';
    public const NO_ISBN = 'isbnNoIsbn';

    
    protected $messageTemplates = [
        self::INVALID => 'Invalid type given. String or integer expected',
        self::NO_ISBN => 'The input is not a valid ISBN number',
    ];

    
    protected $options = [
        'type'      => self::AUTO, 
        'separator' => '', 
    ];

    
    protected function detectFormat()
    {
        
        $sep      = quotemeta($this->getSeparator());
        $patterns = [];
        $lengths  = [];
        $type     = $this->getType();

        
        if ($type === self::ISBN10 || $type === self::AUTO) {
            if (empty($sep)) {
                $pattern = '/^[0-9]{9}[0-9X]{1}$/';
                $length  = 10;
            } else {
                $pattern = "/^[0-9]{1,7}[{$sep}]{1}[0-9]{1,7}[{$sep}]{1}[0-9]{1,7}[{$sep}]{1}[0-9X]{1}$/";
                $length  = 13;
            }

            $patterns[$pattern] = self::ISBN10;
            $lengths[$pattern]  = $length;
        }

        
        if ($type === self::ISBN13 || $type === self::AUTO) {
            if (empty($sep)) {
                $pattern = '/^[0-9]{13}$/';
                $length  = 13;
            } else {
                
                $pattern = "/^[0-9]{1,9}[{$sep}]{1}[0-9]{1,5}[{$sep}]{1}[0-9]{1,9}[{$sep}]{1}[0-9]{1,9}[{$sep}]{1}[0-9]{1}$/";
                
                $length = 17;
            }

            $patterns[$pattern] = self::ISBN13;
            $lengths[$pattern]  = $length;
        }

        
        foreach ($patterns as $pattern => $type) {
            if ((strlen($this->getValue()) === $lengths[$pattern]) && preg_match($pattern, $this->getValue())) {
                return $type;
            }
        }

        return null;
    }

    
    public function isValid($value)
    {
        if (! is_string($value) && ! is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $value = (string) $value;
        $this->setValue($value);

        switch ($this->detectFormat()) {
            case self::ISBN10:
                $isbn = new Isbn\Isbn10();
                break;

            case self::ISBN13:
                $isbn = new Isbn\Isbn13();
                break;

            default:
                $this->error(self::NO_ISBN);
                return false;
        }

        $value    = str_replace($this->getSeparator(), '', $value);
        $checksum = $isbn->getChecksum($value);

        
        if (substr($this->getValue(), -1) !== (string) $checksum) {
            $this->error(self::NO_ISBN);
            return false;
        }
        return true;
    }

    
    public function setSeparator($separator)
    {
        
        if (! in_array($separator, ['-', ' ', ''])) {
            throw new Exception\InvalidArgumentException('Invalid ISBN separator.');
        }

        $this->options['separator'] = $separator;
        return $this;
    }

    
    public function getSeparator()
    {
        return $this->options['separator'];
    }

    
    public function setType($type)
    {
        
        if (! in_array($type, [self::AUTO, self::ISBN10, self::ISBN13])) {
            throw new Exception\InvalidArgumentException('Invalid ISBN type');
        }

        $this->options['type'] = $type;
        return $this;
    }

    
    public function getType()
    {
        return $this->options['type'];
    }
}
