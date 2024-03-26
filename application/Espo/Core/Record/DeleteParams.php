<?php


namespace Espo\Core\Record;


class DeleteParams
{
    public function __construct() {}

    public static function create(): self
    {
        return new self();
    }
}
