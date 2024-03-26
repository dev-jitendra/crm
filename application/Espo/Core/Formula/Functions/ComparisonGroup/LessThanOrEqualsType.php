<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

class LessThanOrEqualsType extends Base
{
    
    protected function compare($left, $right)
    {
        return $left <= $right;
    }
}
