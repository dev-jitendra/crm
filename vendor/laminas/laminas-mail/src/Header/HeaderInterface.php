<?php

namespace Laminas\Mail\Header;

interface HeaderInterface
{
    
    public const FORMAT_ENCODED = true;

    
    public const FORMAT_RAW = false;

    
    public static function fromString($headerLine);

    
    public function getFieldName();

    
    public function getFieldValue($format = self::FORMAT_RAW);

    
    public function setEncoding($encoding);

    
    public function getEncoding();

    
    public function toString();
}
