<?php


namespace Espo\Core\Authentication\Logout;


class Params
{
    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }
}
