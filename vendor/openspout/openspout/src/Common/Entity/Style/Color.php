<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Common\Exception\InvalidColorException;


final class Color
{
    
    public const BLACK = '000000';
    public const WHITE = 'FFFFFF';
    public const RED = 'FF0000';
    public const DARK_RED = 'C00000';
    public const ORANGE = 'FFC000';
    public const YELLOW = 'FFFF00';
    public const LIGHT_GREEN = '92D040';
    public const GREEN = '00B050';
    public const LIGHT_BLUE = '00B0E0';
    public const BLUE = '0070C0';
    public const DARK_BLUE = '002060';
    public const PURPLE = '7030A0';

    
    public static function rgb(int $red, int $green, int $blue): string
    {
        self::throwIfInvalidColorComponentValue($red);
        self::throwIfInvalidColorComponentValue($green);
        self::throwIfInvalidColorComponentValue($blue);

        return strtoupper(
            self::convertColorComponentToHex($red).
            self::convertColorComponentToHex($green).
            self::convertColorComponentToHex($blue)
        );
    }

    
    public static function toARGB(string $rgbColor): string
    {
        return 'FF'.$rgbColor;
    }

    
    private static function throwIfInvalidColorComponentValue(int $colorComponent): void
    {
        if ($colorComponent < 0 || $colorComponent > 255) {
            throw new InvalidColorException("The RGB components must be between 0 and 255. Received: {$colorComponent}");
        }
    }

    
    private static function convertColorComponentToHex(int $colorComponent): string
    {
        return str_pad(dechex($colorComponent), 2, '0', STR_PAD_LEFT);
    }
}
