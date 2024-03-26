<?php


namespace Espo\ORM\Query\Part\Expression;

use Espo\ORM\Query\Part\Expression;

class Util
{
    
    public static function composeFunction(
        string $function,
        Expression|bool|int|float|string|null ...$arguments
    ): Expression {

        $stringifiedItems = array_map(
            function ($item) {
                return self::stringifyArgument($item);
            },
            $arguments
        );

        $expression = $function . ':(' . implode(', ', $stringifiedItems) . ')';

        return Expression::create($expression);
    }

    
    public static function stringifyArgument(Expression|bool|int|float|string|null $argument): string
    {

        if ($argument instanceof Expression) {
            return $argument->getValue();
        }

        if (is_null($argument)) {
            return 'NULL';
        }

       if (is_bool($argument)) {
            return $argument ? 'TRUE': 'FALSE';
        }

       if (is_int($argument)) {
           return strval($argument);
       }

       if (is_float($argument)) {
           return strval($argument);
       }

       return '\'' . str_replace('\'', '\\\'', $argument) . '\'';
    }
}
