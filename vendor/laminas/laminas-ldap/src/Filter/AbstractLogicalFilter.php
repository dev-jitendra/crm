<?php

namespace Laminas\Ldap\Filter;

use function is_string;


abstract class AbstractLogicalFilter extends AbstractFilter
{
    public const TYPE_AND = '&';
    public const TYPE_OR  = '|';

    
    private array $subfilters;

    
    private string $symbol;

    
    protected function __construct(array $subfilters, $symbol)
    {
        foreach ($subfilters as $key => $s) {
            if (is_string($s)) {
                $subfilters[$key] = new StringFilter($s);
            } elseif (! $s instanceof AbstractFilter) {
                throw new Exception\FilterException('Only strings or Laminas\Ldap\Filter\AbstractFilter allowed.');
            }
        }
        $this->subfilters = $subfilters;
        $this->symbol     = $symbol;
    }

    
    public function addFilter(AbstractFilter $filter)
    {
        $new               = clone $this;
        $new->subfilters[] = $filter;
        return $new;
    }

    
    public function toString()
    {
        $return = '(' . $this->symbol;
        foreach ($this->subfilters as $sub) {
            $return .= $sub->toString();
        }
        $return .= ')';
        return $return;
    }
}
