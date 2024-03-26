<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class AssignType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $name = $this->evaluate($args[0]);

        if (!is_string($name)) {
            $this->throwBadArgumentValue(1, 'string');
        }

        $value = $this->evaluate($args[1]);

        $this->getVariables()->$name = $value;

        return $value;
    }
}
