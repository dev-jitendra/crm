<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class ConditionType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        return $this->evaluate($args[0]);
    }
}
