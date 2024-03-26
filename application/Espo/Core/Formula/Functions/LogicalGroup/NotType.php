<?php


namespace Espo\Core\Formula\Functions\LogicalGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class NotType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        return !$this->evaluate($args[0]);
    }
}
