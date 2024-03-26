<?php


namespace Espo\Core\Record;


class ReadParams
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
}
