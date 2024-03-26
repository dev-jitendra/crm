<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;

class MatchExtractType implements Func
{
    
    public function process(EvaluatedArgumentList $arguments): ?array
    {
        if (count($arguments) < 2) {
            throw TooFewArguments::create(2);
        }

        $string = $arguments[0];
        $pattern = $arguments[1];

        if (!is_string($string)) {
            throw BadArgumentType::create(1, 'string');
        }

        if (!is_string($pattern)) {
            throw BadArgumentType::create(2, 'string');
        }

        $result = preg_match($pattern, $string, $matches);

        if (!$result) {
            return null;
        }

        return array_slice($matches, 1);
    }
}
