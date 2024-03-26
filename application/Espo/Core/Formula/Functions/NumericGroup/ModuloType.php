<?php


namespace Espo\Core\Formula\Functions\NumericGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class ModuloType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $result = $this->evaluate($args[0]);
        $part = $this->evaluate($args[1]);

        if (!is_float($part) && !is_int($part)) {
            $part = floatval($part);
        }

        $result %= $part;

        return $result;
    }
}
