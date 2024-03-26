<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class UniqueType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $evaluatedArgs = $this->evaluate($args);

        if (count($evaluatedArgs) < 1) {
            $this->throwTooFewArguments();
        }

        $array = $evaluatedArgs[0] ?? [];

        if (!is_array($array)) {
            $this->throwBadArgumentType(1, 'array');
        }

        return array_values(array_unique($array));
    }
}
