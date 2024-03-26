<?php


namespace Espo\ORM\Query\Part;

use Espo\ORM\Query\Part\Where\AndGroup;
use Espo\ORM\Query\Part\Where\Comparison;
use Espo\ORM\Query\Part\Where\Exists;
use Espo\ORM\Query\Part\Where\Not;
use Espo\ORM\Query\Part\Where\OrGroup;

use Espo\ORM\Query\Select;


class Condition
{
    private function __construct()
    {}

    
    public static function and(WhereItem ...$items): AndGroup
    {
        return AndGroup::create(...$items);
    }

    
    public static function or(WhereItem ...$items): OrGroup
    {
        return OrGroup::create(...$items);
    }

    
    public static function not(WhereItem $item): Not
    {
        return Not::create($item);
    }

    
    public static function exists(Select $subQuery): Exists
    {
        return Exists::create($subQuery);
    }

    
    public static function column(string $expression): Expression
    {
        return Expression::column($expression);
    }

    
    public static function equal(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): Comparison {

        return Comparison::equal($argument1, $argument2);
    }

    
    public static function notEqual(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): Comparison {

        return Comparison::notEqual($argument1, $argument2);
    }

    
    public static function like(Expression $subject, Expression|string $pattern): Comparison
    {
        return Comparison::like($subject, $pattern);
    }

    
    public static function notLike(Expression $subject, Expression|string $pattern): Comparison
    {
        return Comparison::notLike($subject, $pattern);
    }

    
    public static function greater(
        Expression $argument1,
        Expression|Select|string|int|float $argument2
    ): Comparison {

        return Comparison::greater($argument1, $argument2);
    }

    
    public static function greaterOrEqual(
        Expression $argument1,
        Expression|Select|string|int|float $argument2
    ): Comparison {

        return Comparison::greaterOrEqual($argument1, $argument2);
    }

    
    public static function less(
        Expression $argument1,
        Expression|Select|string|int|float $argument2
    ): Comparison {

        return Comparison::less($argument1, $argument2);
    }

    
    public static function lessOrEqual(
        Expression $argument1,
        Expression|Select|string|int|float $argument2
    ): Comparison {

        return Comparison::lessOrEqual($argument1, $argument2);
    }

    
    public static function in(Expression $subject, Select|array $set): Comparison
    {
        return Comparison::in($subject, $set);
    }

    
    public static function notIn(Expression $subject, Select|array $set): Comparison
    {
        return Comparison::notIn($subject, $set);
    }
}
