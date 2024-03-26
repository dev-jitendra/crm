<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class SubstringType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $string = $args[0];
        $start = $args[1];

        if (count($args) > 2) {
            $length = $args[2];
            return mb_substr($string, $start, $length);
        } else {
            return mb_substr($string, $start);
        }
    }
}
