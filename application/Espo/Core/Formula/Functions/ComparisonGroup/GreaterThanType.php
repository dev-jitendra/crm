<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

class GreaterThanType extends Base
{
    
    protected function compare($left, $right)
    {
        return $left > $right;
    }
}
