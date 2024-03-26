<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class IfThenType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        if ($this->evaluate($args[0])) {
            return $this->evaluate($args[1]);
        }
    }
}
