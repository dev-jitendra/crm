<?php



namespace Carbon\Doctrine;

class DateTimeDefaultPrecision
{
    private static $precision = 6;

    
    public static function set(int $precision): void
    {
        self::$precision = $precision;
    }

    
    public static function get(): int
    {
        return self::$precision;
    }
}
