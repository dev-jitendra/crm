<?php


namespace Espo\Core\Select\Text\FullTextSearch\DataComposer;


class Params
{
    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }
}
