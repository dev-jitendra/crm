<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;

class ContainsType implements Func
{
    public function process(EvaluatedArgumentList $arguments): bool
    {
        if (count($arguments) < 2) {
            throw TooFewArguments::create(2);
        }

        $string = $arguments[0];
        $needle = $arguments[1];

        if (!is_string($string)) {
            return false;
        }

        return mb_strpos($string, $needle) !== false;
    }
}
