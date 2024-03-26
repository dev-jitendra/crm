<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

class GreaterThanOrEqualsType extends Base
{
    
    protected function compare($left, $right)
    {
        return $left >= $right;
    }
}
