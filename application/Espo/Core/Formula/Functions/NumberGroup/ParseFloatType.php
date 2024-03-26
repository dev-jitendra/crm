<?php


namespace Espo\Core\Formula\Functions\NumberGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;

class ParseFloatType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $value = $this->evaluate($args[0]) ?? '0.0';

        if (!is_string($value)) {
            return $this->throwBadArgumentType(1, 'string');
        }

        return floatval($value);
    }
}
