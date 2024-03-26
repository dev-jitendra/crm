<?php

namespace Laminas\Ldap\Filter;


class StringFilter extends AbstractFilter
{
    
    protected $filter;

    
    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    
    public function toString()
    {
        return '(' . $this->filter . ')';
    }
}
