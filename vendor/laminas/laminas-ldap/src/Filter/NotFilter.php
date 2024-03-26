<?php

namespace Laminas\Ldap\Filter;


class NotFilter extends AbstractFilter
{
    
    private AbstractFilter $filter;

    
    public function __construct(AbstractFilter $filter)
    {
        $this->filter = $filter;
    }

    
    public function negate()
    {
        return $this->filter;
    }

    
    public function toString()
    {
        return '(!' . $this->filter->toString() . ')';
    }
}
