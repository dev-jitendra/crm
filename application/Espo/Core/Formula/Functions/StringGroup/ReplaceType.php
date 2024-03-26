<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class ReplaceType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 3) {
            $this->throwTooFewArguments();
        };

        $string = $args[0];
        $search = $args[1];
        $replace = $args[2];

        if (!is_string($string)) {
            $this->logBadArgumentType(1, 'string');
            return '';
        }

        if (!is_string($search)) {
            $this->logBadArgumentType(2, 'string');
            return $string;
        }

        if (!is_string($replace)) {
            $this->logBadArgumentType(3, 'string');
            return $string;
        }

        return str_replace($search, $replace, $string);
    }
}
