<?php

namespace Laminas\Ldap\Filter;

use Laminas\Ldap\Converter\Converter;

use function array_merge;
use function count;
use function func_get_args;
use function is_array;
use function str_replace;


abstract class AbstractFilter
{
    
    abstract public function toString();

    
    public function __toString()
    {
        return $this->toString();
    }

    
    public function negate()
    {
        return new NotFilter($this);
    }

    
    public function addAnd($filter)
    {
        $fa   = func_get_args();
        $args = array_merge([$this], $fa);
        return new AndFilter($args);
    }

    
    public function addOr($filter)
    {
        $fa   = func_get_args();
        $args = array_merge([$this], $fa);
        return new OrFilter($args);
    }

    
    public static function escapeValue($values = [])
    {
        if (! is_array($values)) {
            $values = [$values];
        }
        foreach ($values as $key => $val) {
            
            $val = str_replace(['\\', '*', '(', ')'], ['\5c', '\2a', '\28', '\29'], $val);
            
            $val = Converter::ascToHex32($val);
            if (null === $val) {
                $val = '\0'; 
            }
            $values[$key] = $val;
        }
        return count($values) === 1 ? $values[0] : $values;
    }

    
    public static function unescapeValue($values = [])
    {
        if (! is_array($values)) {
            $values = [$values];
        }
        foreach ($values as $key => $value) {
            
            $values[$key] = Converter::hex32ToAsc($value);
        }
        return count($values) === 1 ? $values[0] : $values;
    }
}
