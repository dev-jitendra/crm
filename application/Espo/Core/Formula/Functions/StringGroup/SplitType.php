<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\ArgumentList;

class SplitType extends BaseFunction
{
    
    public function process(ArgumentList $args)
    {
        $evaluatedArgs = $this->evaluate($args);

        if (count($evaluatedArgs) < 2) {
            $this->throwTooFewArguments(2);
        }

        $string = $evaluatedArgs[0] ?? '';
        $separator = $evaluatedArgs[1];

        if (!is_string($string)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_string($separator)) {
            $this->throwBadArgumentType(2, 'string');
        }

        if ($separator === '') {
            return mb_str_split($string);
        }

        return explode($separator, $string);
    }
}
