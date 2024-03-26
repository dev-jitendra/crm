<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class TestType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $string = $args[0];
        $regexp = $args[1];

        if (!is_string($string)) {
            return false;
        }
        if (!is_string($regexp)) {
            return false;
        }

        return !!preg_match($regexp, $string);
    }
}
