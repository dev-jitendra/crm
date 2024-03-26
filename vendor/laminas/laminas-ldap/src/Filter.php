<?php

namespace Laminas\Ldap;

use function func_get_args;


class Filter extends Filter\StringFilter
{
    public const TYPE_EQUALS         = '=';
    public const TYPE_GREATER        = '>';
    public const TYPE_GREATEROREQUAL = '>=';
    public const TYPE_LESS           = '<';
    public const TYPE_LESSOREQUAL    = '<=';
    public const TYPE_APPROX         = '~=';

    
    public static function equals($attr, $value)
    {
        return new static($attr, $value, self::TYPE_EQUALS, null, null);
    }

    
    public static function begins($attr, $value)
    {
        return new static($attr, $value, self::TYPE_EQUALS, null, '*');
    }

    
    public static function ends($attr, $value)
    {
        return new static($attr, $value, self::TYPE_EQUALS, '*', null);
    }

    
    public static function contains($attr, $value)
    {
        return new static($attr, $value, self::TYPE_EQUALS, '*', '*');
    }

    
    public static function greater($attr, $value)
    {
        return new static($attr, $value, self::TYPE_GREATER, null, null);
    }

    
    public static function greaterOrEqual($attr, $value)
    {
        return new static($attr, $value, self::TYPE_GREATEROREQUAL, null, null);
    }

    
    public static function less($attr, $value)
    {
        return new static($attr, $value, self::TYPE_LESS, null, null);
    }

    
    public static function lessOrEqual($attr, $value)
    {
        return new static($attr, $value, self::TYPE_LESSOREQUAL, null, null);
    }

    
    public static function approx($attr, $value)
    {
        return new static($attr, $value, self::TYPE_APPROX, null, null);
    }

    
    public static function any($attr)
    {
        return new static($attr, '', self::TYPE_EQUALS, '*', null);
    }

    
    public static function string($filter)
    {
        return new Filter\StringFilter($filter);
    }

    
    public static function mask($mask, $value)
    {
        return new Filter\MaskFilter($mask, $value);
    }

    
    public static function andFilter($filter)
    {
        return new Filter\AndFilter(func_get_args());
    }

    
    public static function orFilter($filter)
    {
        return new Filter\OrFilter(func_get_args());
    }

    
    private static function createFilterString($attr, $value, $filtertype, $prepend = null, $append = null)
    {
        $str = $attr . $filtertype;
        if ($prepend !== null) {
            $str .= $prepend;
        }
        $str .= ldap_escape($value, '', LDAP_ESCAPE_FILTER);
        if ($append !== null) {
            $str .= $append;
        }
        return $str;
    }

    
    public function __construct($attr, $value, $filtertype, $prepend = null, $append = null)
    {
        $filter = static::createFilterString($attr, $value, $filtertype, $prepend, $append);
        parent::__construct($filter);
    }
}
