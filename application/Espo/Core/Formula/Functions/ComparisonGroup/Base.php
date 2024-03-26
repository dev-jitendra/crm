<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

abstract class Base extends BaseFunction
{
    
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $left = $this->evaluate($args[0]);
        $right = $this->evaluate($args[1]);

        return $this->compare($left, $right);
    }

    
    abstract protected function compare($left, $right);
}
