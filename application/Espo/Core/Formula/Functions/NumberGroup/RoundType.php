<?php


namespace Espo\Core\Formula\Functions\NumberGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class RoundType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $value = $this->evaluate($args[0]);

        $precision = 0;

        if (count($args) > 1) {
             $precision = $this->evaluate($args[1]);
        }

        if (!is_numeric($value)) {
            return null;
        }

        return round((float) $value, $precision);
    }
}
