<?php

namespace Laminas\Validator;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

class InArray extends AbstractValidator
{
    public const NOT_IN_ARRAY = 'notInArray';

    
    
    public const COMPARE_STRICT = 1;

    
    public const COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY = 0;

    
    public const COMPARE_NOT_STRICT = -1;

    
    protected $messageTemplates = [
        self::NOT_IN_ARRAY => 'The input was not found in the haystack',
    ];

    
    protected $haystack;

    
    protected $strict = self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY;

    
    protected $recursive = false;

    
    public function getHaystack()
    {
        if ($this->haystack === null) {
            throw new Exception\RuntimeException('haystack option is mandatory');
        }
        return $this->haystack;
    }

    
    public function setHaystack(array $haystack)
    {
        $this->haystack = $haystack;
        return $this;
    }

    
    public function getStrict()
    {
        
        if (
            $this->strict === self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY
            || $this->strict === self::COMPARE_STRICT
        ) {
            return (bool) $this->strict;
        }
        return $this->strict;
    }

    
    public function setStrict($strict)
    {
        if (is_bool($strict)) {
            $strict = $strict ? self::COMPARE_STRICT : self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY;
        }

        $checkTypes = [
            self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY, 
            self::COMPARE_STRICT, 
            self::COMPARE_NOT_STRICT, 
        ];

        
        if (! in_array($strict, $checkTypes)) {
            throw new Exception\InvalidArgumentException('Strict option must be one of the COMPARE_ constants');
        }

        $this->strict = $strict;
        return $this;
    }

    
    public function getRecursive()
    {
        return $this->recursive;
    }

    
    public function setRecursive($recursive)
    {
        $this->recursive = (bool) $recursive;
        return $this;
    }

    
    public function isValid($value)
    {
        
        $haystack = $this->getHaystack();

        
        
        if (
            self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY === $this->strict
            && (is_int($value) || is_float($value))
        ) {
            $value = (string) $value;
        }

        $this->setValue($value);

        if ($this->getRecursive()) {
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));
            foreach ($iterator as $element) {
                if (self::COMPARE_STRICT === $this->strict) {
                    if ($element === $value) {
                        return true;
                    }

                    continue;
                }

                
                $el = $element;
                if (
                    self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY === $this->strict
                    && is_string($value) && (is_int($el) || is_float($el))
                ) {
                    $el = (string) $el;
                }

                
                if ($el == $value) {
                    return true;
                }
            }

            $this->error(self::NOT_IN_ARRAY);
            return false;
        }

        
        if (
            self::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY === $this->strict
            && is_string($value)
        ) {
            foreach ($haystack as &$h) {
                if (is_int($h) || is_float($h)) {
                    $h = (string) $h;
                }
            }

            if (in_array($value, $haystack, (bool) $this->strict)) {
                return true;
            }

            $this->error(self::NOT_IN_ARRAY);
            return false;
        }

        if (in_array($value, $haystack, self::COMPARE_STRICT === $this->strict)) {
            return true;
        }

        if (self::COMPARE_NOT_STRICT === $this->strict) {
            return true;
        }

        $this->error(self::NOT_IN_ARRAY);
        return false;
    }
}
