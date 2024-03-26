<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

class LessThanType extends Base
{
    
    protected function compare($left, $right)
    {
        return $left < $right;
    }
}
