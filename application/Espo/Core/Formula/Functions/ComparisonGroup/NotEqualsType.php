<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

class NotEqualsType extends EqualsType
{
    
    protected function compare($left, $right)
    {
        return !parent::compare($left, $right);
    }
}
