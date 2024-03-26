<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Select;

use RuntimeException;


class Comparison implements WhereItem
{
    private const OPERATOR_EQUAL = '=';
    private const OPERATOR_NOT_EQUAL = '!=';
    private const OPERATOR_GREATER = '>';
    private const OPERATOR_GREATER_OR_EQUAL = '>=';
    private const OPERATOR_LESS = '<';
    private const OPERATOR_LESS_OR_EQUAL = '<=';
    private const OPERATOR_LIKE = '*';
    private const OPERATOR_NOT_LIKE = '!*';
    private const OPERATOR_IN_SUB_QUERY = '=s';
    private const OPERATOR_NOT_IN_SUB_QUERY = '!=s';
    private const OPERATOR_NOT_EQUAL_ANY = '!=any';
    private const OPERATOR_GREATER_ANY = '>any';
    private const OPERATOR_GREATER_OR_EQUAL_ANY = '>=any';
    private const OPERATOR_LESS_ANY = '<any';
    private const OPERATOR_LESS_OR_EQUAL_ANY = '<=any';
    private const OPERATOR_EQUAL_ALL = '=all';
    private const OPERATOR_GREATER_ALL = '>all';
    private const OPERATOR_GREATER_OR_EQUAL_ALL = '>=all';
    private const OPERATOR_LESS_ALL = '<all';
    private const OPERATOR_LESS_OR_EQUAL_ALL = '<=all';

    private string $rawKey;
    private mixed $rawValue;

    private function __construct(string $rawKey, mixed $rawValue)
    {
        $this->rawKey = $rawKey;
        $this->rawValue = $rawValue;
    }

    public function getRaw(): array
    {
        return [$this->rawKey => $this->rawValue];
    }

    public function getRawKey(): string
    {
        return $this->rawKey;
    }

    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }

    
    public static function equal(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        return self::createComparison(self::OPERATOR_EQUAL, $argument1, $argument2);
    }

    
    public static function notEqual(
        Expression $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        return self::createComparison(self::OPERATOR_NOT_EQUAL, $argument1, $argument2);
    }

    
    public static function like(Expression $subject, Expression|string $pattern): self
    {
        return self::createComparison(self::OPERATOR_LIKE, $subject, $pattern);
    }

    
    public static function notLike(Expression $subject, Expression|string $pattern): self
    {
        return self::createComparison(self::OPERATOR_NOT_LIKE, $subject, $pattern);
    }

    
    public static function greater(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_GREATER, $argument1, $argument2);
    }

    
    public static function greaterOrEqual(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL, $argument1, $argument2);
    }

    
    public static function less(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_LESS, $argument1, $argument2);
    }

    
    public static function lessOrEqual(Expression $argument1, Expression|Select|string|int|float $argument2): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL, $argument1, $argument2);
    }

    
    public static function in(Expression $subject, Select|array $set): self
    {
        if ($set instanceof Select) {
            return self::createInOrNotInSubQuery(self::OPERATOR_IN_SUB_QUERY, $subject, $set);
        }

        return self::createInOrNotInArray(self::OPERATOR_EQUAL, $subject, $set);
    }

    
    public static function notIn(Expression $subject, Select|array $set): self
    {
        if ($set instanceof Select) {
            return self::createInOrNotInSubQuery(self::OPERATOR_NOT_IN_SUB_QUERY, $subject, $set);
        }

        return self::createInOrNotInArray(self::OPERATOR_NOT_EQUAL, $subject, $set);
    }

    
    public static function notEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_NOT_EQUAL_ANY, $argument, $subQuery);
    }

    
    public static function greaterAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_ANY, $argument, $subQuery);
    }

    
    public static function lessAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_ANY, $argument, $subQuery);
    }

    
    public static function greaterOrEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL_ANY, $argument, $subQuery);
    }

    
    public static function lessOrEqualAny(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL_ANY, $argument, $subQuery);
    }

    
    public static function equalAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_EQUAL_ALL, $argument, $subQuery);
    }

    
    public static function greaterAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_ALL, $argument, $subQuery);
    }

    
    public static function lessAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_ALL, $argument, $subQuery);
    }

    
    public static function greaterOrEqualAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_GREATER_OR_EQUAL_ALL, $argument, $subQuery);
    }

    
    public static function lessOrEqualAll(Expression $argument, Select $subQuery): self
    {
        return self::createComparison(self::OPERATOR_LESS_OR_EQUAL_ALL, $argument, $subQuery);
    }

    private static function createComparison(
        string $operator,
        Expression|string $argument1,
        Expression|Select|string|int|float|bool|null $argument2
    ): self {

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }
        }
        else {
            $key = $argument1->getValue();
        }

        if (str_ends_with($key, ':')) {
            throw new RuntimeException("Expression should not end with `:`.");
        }

        $key .= $operator;

        if ($argument2 instanceof Expression) {
            $key .= ':';

            $value = $argument2->getValue();
        }
        else {
            $value = $argument2;
        }

        return new self($key, $value);
    }

    
    private static function createInOrNotInArray(
        string $operator,
        Expression|string $argument1,
        array $valueList
    ): self {

        foreach ($valueList as $item) {
            if (!is_scalar($item)) {
                throw new RuntimeException("Array items must be scalar.");
            }
        }

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }

            if (str_ends_with($key, ':')) {
                throw new RuntimeException("Expression can't end with `:`.");
            }
        }
        else {
            $key = $argument1->getValue();
        }

        $key .= $operator;

        return new self($key, $valueList);
    }

    private static function createInOrNotInSubQuery(
        string $operator,
        Expression|string $argument1,
        Select $query
    ): self {

        if (is_string($argument1)) {
            $key = $argument1;

            if ($key === '') {
                throw new RuntimeException("Expression can't be empty.");
            }

            if (str_ends_with($key, ':')) {
                throw new RuntimeException("Expression can't end with `:`.");
            }
        }
        else {
            $key = $argument1->getValue();
        }

        $key .= $operator;

        return new self($key, $query);
    }
}
