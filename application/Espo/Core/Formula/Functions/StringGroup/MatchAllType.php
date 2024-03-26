<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

class MatchAllType extends BaseFunction
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
            return null;
        }

        if (!is_string($regexp)) {
            return null;
        }

        $offset = $args[2] ?? 0;

        $result = preg_match_all($regexp, $string, $matches, \PREG_PATTERN_ORDER, $offset);

        if (!$result) {
            return null;
        }

        if (!count($matches)) {
            return null;
        }

        return $matches[0];
    }
}
