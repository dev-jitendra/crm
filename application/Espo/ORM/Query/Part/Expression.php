<?php


namespace Espo\ORM\Query\Part;

use Espo\ORM\Query\Part\Expression\Util;

use RuntimeException;


class Expression implements WhereItem
{
    private string $expression;

    public function __construct(string $expression)
    {
        if ($expression === '') {
            throw new RuntimeException("Expression can't be empty.");
        }

        if (str_ends_with($expression, ':')) {
            throw new RuntimeException("Expression should not end with `:`.");
        }

        $this->expression = $expression;
    }

    public function getRaw(): array
    {
        return [$this->getRawKey() => null];
    }

    public function getRawKey(): string
    {
        return $this->expression . ':';
    }

    public function getRawValue(): mixed
    {
        return null;
    }

    
    public function getValue(): string
    {
        return $this->expression;
    }

    
    public static function create(string $expression): self
    {
        return new self($expression);
    }

    
    public static function value(string|float|int|bool|null $value): self
    {
        return self::create(self::stringifyArgument($value));
    }

    
    public static function column(string $expression): self
    {
        $string = $expression;

        if (strlen($string) && $string[0] === '@') {
            $string = substr($string, 1);
        }

        if ($string === '') {
            throw new RuntimeException("Empty column.");
        }

        if (!preg_match('/^[a-zA-Z\d.]+$/', $string)) {
            throw new RuntimeException("Bad column. Must be of letters, digits. Can have a dot.");
        }

        return self::create($expression);
    }

    
    public static function alias(string $expression): self
    {
        if ($expression === '') {
            throw new RuntimeException("Empty alias.");
        }

        if (!preg_match('/^[a-zA-Z\d.]+$/', $expression)) {
            throw new RuntimeException("Bad alias expression. Must be of letters, digits. Can have a dot.");
        }

        if (str_contains($expression, '.')) {
            [$left, $right] = explode('.', $expression, 2);

            return self::create($left . '.#' . $right);
        }

        return self::create('#' . $expression);
    }

    
    public static function count(Expression $expression): self
    {
        return self::composeFunction('COUNT', $expression);
    }

    
    public static function min(Expression $expression): self
    {
        return self::composeFunction('MIN', $expression);
    }

    
    public static function max(Expression $expression): self
    {
        return self::composeFunction('MAX', $expression);
    }

    
    public static function sum(Expression $expression): self
    {
        return self::composeFunction('SUM', $expression);
    }

    
    public static function average(Expression $expression): self
    {
        return self::composeFunction('AVG', $expression);
    }

    
    public static function if(
        Expression $condition,
        Expression|string|int|float|bool|null $then,
        Expression|string|int|float|bool|null $else
    ): self {

        return self::composeFunction('IF', $condition, $then, $else);
    }

    
    public static function switch(Expression|string|int|float|bool|null ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('SWITCH', ...$arguments);
    }

    
    public static function map(Expression|string|int|float|bool|null ...$arguments): self
    {
        if (count($arguments) < 3) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('MAP', ...$arguments);
    }

    
    public static function ifNull(Expression $value, Expression|string|int|float|bool $fallbackValue): self
    {
        return self::composeFunction('IFNULL', $value, $fallbackValue);
    }

    
    public static function nullIf(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('NULLIF', $argument1, $argument2);
    }

    
    public static function like(Expression $subject, Expression|string $pattern): self
    {
        return self::composeFunction('LIKE', $subject, $pattern);
    }

    
    public static function equal(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('EQUAL', $argument1, $argument2);
    }

    
    public static function notEqual(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('NOT_EQUAL', $argument1, $argument2);
    }

    
    public static function greater(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('GREATER_THAN', $argument1, $argument2);
    }

    
    public static function less(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('LESS_THAN', $argument1, $argument2);
    }

    
    public static function greaterOrEqual(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('GREATER_THAN_OR_EQUAL', $argument1, $argument2);
    }

    
    public static function lessOrEqual(
        Expression|string|int|float|bool $argument1,
        Expression|string|int|float|bool $argument2
    ): self {

        return self::composeFunction('LESS_THAN_OR_EQUAL', $argument1, $argument2);
    }

    
    public static function isNull(Expression $expression): self
    {
        return self::composeFunction('IS_NULL', $expression);
    }

    
    public static function isNotNull(Expression $expression): self
    {
        return self::composeFunction('IS_NOT_NULL', $expression);
    }

    
    public static function in(Expression $expression, array $values): self
    {
        return self::composeFunction('IN', $expression, ...$values);
    }

    
    public static function notIn(Expression $expression, array $values): self
    {
        return self::composeFunction('NOT_IN', $expression, ...$values);
    }

    
    public static function coalesce(Expression ...$expressions): self
    {
        return self::composeFunction('COALESCE', ...$expressions);
    }

    
    public static function month(Expression $date): self
    {
        return self::composeFunction('MONTH_NUMBER', $date);
    }

    
    public static function week(Expression $date, int $weekStart = 0): self
    {
        if ($weekStart !== 0 && $weekStart !== 1) {
            throw new RuntimeException("Week start can be only 0 or 1.");
        }

        if ($weekStart === 1) {
            return self::composeFunction('WEEK_NUMBER_1', $date);
        }

        return self::composeFunction('WEEK_NUMBER', $date);
    }

    
    public static function dayOfWeek(Expression $date): self
    {
        return self::composeFunction('DAYOFWEEK', $date);
    }

    
    public static function dayOfMonth(Expression $date): self
    {
        return self::composeFunction('DAYOFMONTH', $date);
    }

    
    public static function year(Expression $date): self
    {
        return self::composeFunction('YEAR', $date);
    }

    
    public static function yearFiscal(Expression $date, int $fiscalYearStart = 1): self
    {
        if ($fiscalYearStart < 1 || $fiscalYearStart > 12) {
            throw new RuntimeException("Bad fiscal year start.");
        }

        return self::composeFunction('YEAR_' . strval($fiscalYearStart), $date);
    }

    
    public static function quarter(Expression $date): self
    {
        return self::composeFunction('QUARTER_NUMBER', $date);
    }

    
    public static function hour(Expression $dateTime): self
    {
        return self::composeFunction('HOUR', $dateTime);
    }

    
    public static function minute(Expression $dateTime): self
    {
        return self::composeFunction('MINUTE', $dateTime);
    }

    
    public static function second(Expression $dateTime): self
    {
        return self::composeFunction('SECOND', $dateTime);
    }

    
    public static function now(): self
    {
        return self::composeFunction('NOW');
    }

    
    public static function date(Expression $dateTime): self
    {
        return self::composeFunction('DATE', $dateTime);
    }

    
    public static function convertTimezone(Expression $date, float $offset): self
    {
        return self::composeFunction('TZ', $date, $offset);
    }

    
    public static function concat(Expression|string ...$strings): self
    {
        return self::composeFunction('CONCAT', ...$strings);
    }

    
    public static function left(Expression $string, int $offset): self
    {
        return self::composeFunction('LEFT', $string, $offset);
    }

    
    public static function lowerCase(Expression $string): self
    {
        return self::composeFunction('LOWER', $string);
    }

    
    public static function upperCase(Expression $string): self
    {
        return self::composeFunction('UPPER', $string);
    }

    
    public static function trim(Expression $string): self
    {
        return self::composeFunction('TRIM', $string);
    }

    
    public static function binary(Expression $string): self
    {
        return self::composeFunction('BINARY', $string);
    }

    
    public static function charLength(Expression $string): self
    {
        return self::composeFunction('CHAR_LENGTH', $string);
    }

    
    public static function replace(
        Expression $haystack,
        Expression|string $needle,
        Expression|string $replaceWith
    ): self {

        return self::composeFunction('REPLACE', $haystack, $needle, $replaceWith);
    }

    
    public static function positionInList(Expression $expression, array $list): self
    {
        return self::composeFunction('POSITION_IN_LIST', $expression, ...$list);
    }

    
    public static function add(Expression|int|float ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('ADD', ...$arguments);
    }

    
    public static function subtract(Expression|int|float ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('SUB', ...$arguments);
    }

    
    public static function multiply(Expression|int|float ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('MUL', ...$arguments);
    }

    
    public static function divide(Expression|int|float ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('DIV', ...$arguments);
    }

    
    public static function modulo(Expression|int|float ...$arguments): self
    {
        if (count($arguments) < 2) {
            throw new RuntimeException("Too few arguments.");
        }

        return self::composeFunction('MOD', ...$arguments);
    }

    
    public static function floor(Expression $number): self
    {
        return self::composeFunction('FLOOR', $number);
    }

    
    public static function ceil(Expression $number): self
    {
        return self::composeFunction('CEIL', $number);
    }

    
    public static function round(Expression $number, int $precision = 0): self
    {
        return self::composeFunction('ROUND', $number, $precision);
    }

    
    public static function greatest(Expression ...$arguments): self
    {
        return self::composeFunction('GREATEST', ...$arguments);
    }

    
    public static function least(Expression ...$arguments): self
    {
        return self::composeFunction('LEAST', ...$arguments);
    }

    
    public static function and(Expression ...$arguments): self
    {
        return self::composeFunction('AND', ...$arguments);
    }

    
    public static function or(Expression ...$arguments): self
    {
        return self::composeFunction('OR', ...$arguments);
    }

    
    public static function not(Expression $argument): self
    {
        return self::composeFunction('NOT', $argument);
    }

    
    public static function row(Expression ...$arguments): self
    {
        return self::composeFunction('ROW', ...$arguments);
    }

    private static function composeFunction(
        string $function,
        Expression|bool|int|float|string|null ...$arguments
    ): self {

        return Util::composeFunction($function, ...$arguments);
    }

    private static function stringifyArgument(Expression|bool|int|float|string|null $argument): string
    {
        return Util::stringifyArgument($argument);
    }
}
