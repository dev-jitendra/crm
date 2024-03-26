<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class IndexOfType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $evaluatedArgs = $this->evaluate($args);

        if (count($evaluatedArgs) < 2) {
            $this->throwTooFewArguments();
        }

        $array = $evaluatedArgs[0] ?? [];
        $needle = $evaluatedArgs[1];

        if (!is_array($array)) {
            $this->throwBadArgumentType(1, 'array');
        }

        $result = array_search($needle, $array, true);

        if ($result === false || is_string($result)) {
            return null;
        }

        return $result;
    }
}
