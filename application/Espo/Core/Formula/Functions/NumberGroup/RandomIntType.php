<?php


namespace Espo\Core\Formula\Functions\NumberGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class RandomIntType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        $min = $args[0] ?? 0;
        $max = $args[1] ?? PHP_INT_MAX;

        if (!is_int($min)) {
            $this->throwBadArgumentType(1, 'int');
        }

        if (!is_int($max)) {
            $this->throwBadArgumentType(2, 'int');
        }

        return random_int($min, $max);
    }
}
