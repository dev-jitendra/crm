<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class LengthType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $value = $this->evaluate($args[0]);

        if (!is_string($value)) {
            $value = strval($value);
        }

        return mb_strlen($value);
    }
}
