<?php

namespace Laminas\Ldap\Filter;


class AndFilter extends AbstractLogicalFilter
{
    
    public function __construct(array $subfilters)
    {
        parent::__construct($subfilters, self::TYPE_AND);
    }
}
