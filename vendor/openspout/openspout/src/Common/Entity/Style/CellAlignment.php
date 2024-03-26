<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;


final class CellAlignment
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const CENTER = 'center';
    public const JUSTIFY = 'justify';

    private const VALID_ALIGNMENTS = [
        self::LEFT => 1,
        self::RIGHT => 1,
        self::CENTER => 1,
        self::JUSTIFY => 1,
    ];

    
    public static function isValid(string $cellAlignment): bool
    {
        return isset(self::VALID_ALIGNMENTS[$cellAlignment]);
    }
}
