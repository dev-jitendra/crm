<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;


final class StringHelper
{
    
    private readonly bool $hasMbstringSupport;

    public function __construct(bool $hasMbstringSupport)
    {
        $this->hasMbstringSupport = $hasMbstringSupport;
    }

    public static function factory(): self
    {
        return new self(\function_exists('mb_strlen'));
    }

    
    public function getStringLength(string $string): int
    {
        return $this->hasMbstringSupport
            ? mb_strlen($string)
            : \strlen($string); 
    }

    
    public function getCharFirstOccurrencePosition(string $char, string $string): int
    {
        $position = $this->hasMbstringSupport
            ? mb_strpos($string, $char)
            : strpos($string, $char); 

        return (false !== $position) ? $position : -1;
    }

    
    public function getCharLastOccurrencePosition(string $char, string $string): int
    {
        $position = $this->hasMbstringSupport
            ? mb_strrpos($string, $char)
            : strrpos($string, $char); 

        return (false !== $position) ? $position : -1;
    }
}
