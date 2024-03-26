<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class PosType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $string = $args[0];
        $needle = $args[1];

        if (!is_string($string)) {
            return false;
        }

        return mb_strpos($string, $needle);
    }
}
