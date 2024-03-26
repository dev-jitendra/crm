<?php


namespace Espo\Core\Formula\Functions\NumericGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class MultiplicationType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $result = 1;
        foreach ($args as $subItem) {
            $part = $this->evaluate($subItem);

            if (!is_float($part) && !is_int($part)) {
                $part = floatval($part);
            }

            $result *= $part;
        }

        return $result;
    }
}
