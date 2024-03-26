<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class RemoveAtType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $evaluatedArgs = $this->evaluate($args);

        if (count($evaluatedArgs) < 2) {
            $this->throwTooFewArguments();
        }

        $array = $evaluatedArgs[0] ?? [];
        $index = $evaluatedArgs[1];

        if ($index === null) {
            return $array;
        }

        if (!is_array($array)) {
            $this->throwBadArgumentType(1, 'array');
        }

        if (!is_int($index)) {
            $this->throwBadArgumentType(2, 'int');
        }

        if (!array_key_exists($index, $array)) {
            return $array;
        }

        unset($array[$index]);

        return array_values($array);
    }
}
