<?php


namespace Espo\Core\Formula\Functions\ComparisonGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;

class NullCoalescingType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $array = [];

        foreach ($args as $arg) {
            $array[] = $arg;
        }

        foreach (array_slice($array, 0, -1) as $arg) {
            $value = $this->evaluate($arg);

            if ($value !== null) {
                return $value;
            }
        }

        return $this->evaluate($array[count($array) - 1]);
    }
}
