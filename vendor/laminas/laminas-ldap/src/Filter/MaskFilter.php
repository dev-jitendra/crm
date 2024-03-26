<?php

namespace Laminas\Ldap\Filter;

use function count;
use function vsprintf;


class MaskFilter extends StringFilter
{
    
    public function __construct($mask, ...$values)
    {
        for ($i = 0, $count = count($values); $i < $count; $i++) {
            $values[$i] = static::escapeValue($values[$i]);
        }
        $filter = vsprintf($mask, $values);
        parent::__construct($filter);
    }

    
    public function toString()
    {
        return $this->filter;
    }
}
