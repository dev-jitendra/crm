<?php


namespace Espo\Core\Formula\Functions\ArrayGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class IncludesType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $list = $args[0] ?? [];
        $needle = $args[1];

        if (!is_array($list)) {
            return false;
        }

        return in_array($needle, $list);
    }
}
