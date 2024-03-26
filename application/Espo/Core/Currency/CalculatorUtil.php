<?php


namespace Espo\Core\Currency;

use DivisionByZeroError;

class CalculatorUtil
{
    private const SCALE = 14;

    public static function add(string $arg1, string $arg2): string
    {
        if (!function_exists('bcadd')) {
            return (string) (
                (float) $arg1 + (float) $arg2
            );
        }

        return bcadd(
            $arg1,
            $arg2,
            self::SCALE
        );
    }

    public static function subtract(string $arg1, string $arg2): string
    {
        if (!function_exists('bcsub')) {
            return (string) (
                (float) $arg1 - (float) $arg2
            );
        }

        return bcsub(
            $arg1,
            $arg2,
            self::SCALE
        );
    }

    public static function multiply(string $arg1, string $arg2): string
    {
        if (!function_exists('bcmul')) {
            return (string) (
                (float) $arg1 * (float) $arg2
            );
        }

        return bcmul(
            $arg1,
            $arg2,
            self::SCALE
        );
    }

    public static function divide(string $arg1, string $arg2): string
    {
        if (!function_exists('bcdiv')) {
            return (string) (
                (float) $arg1 / (float) $arg2
            );
        }

        
        $result = bcdiv(
            $arg1,
            $arg2,
            self::SCALE
        );

        if ($result === null) {
            throw new DivisionByZeroError();
        }

        return $result;
    }

    public static function round(string $arg, int $precision = 0): string
    {
        if (!function_exists('bcadd')) {
            return (string) round((float) $arg, $precision);
        }

        $addition = '0.' . str_repeat('0', $precision) . '5';

        if ($arg[0] === '-') {
            $addition = '-' . $addition;
        }

        return bcadd(
            $arg,
            $addition,
            $precision
        );
    }

    public static function compare(string $arg1, string $arg2): int
    {
        if (!function_exists('bccomp')) {
            return (float) $arg1 <=> (float) $arg2;
        }

        return bccomp(
            $arg1,
            $arg2,
            self::SCALE
        );
    }
}
