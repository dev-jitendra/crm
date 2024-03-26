<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime;




interface EncoderInterface
{
    
    public static function encode(string $binString): string;

    
    public static function decode(string $encodedString, bool $strictPadding = false): string;
}
