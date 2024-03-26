<?php

namespace Laminas\Ldap\Filter;


class OrFilter extends AbstractLogicalFilter
{
    
    public function __construct(array $subfilters)
    {
        parent::__construct($subfilters, self::TYPE_OR);
    }
}
