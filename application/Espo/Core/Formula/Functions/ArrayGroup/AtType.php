<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class AtType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $array = $args[0] ?? [];
        $index = $args[1];

        if (!is_array($array)) {
            $this->throwBadArgumentType(1, 'array');
        }

        if (!is_int($index)) {
            $this->throwBadArgumentType(2, 'int');
        }

        if (!array_key_exists($index, $array)) {
            $this->log("index doesn't exist");
            return null;
        }

        return $array[$index];
    }
}
