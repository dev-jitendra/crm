<?php


namespace Espo\Core\Select\Helpers;

class RandomStringGenerator
{
    public function generate(): string
    {
        return strval(rand(10000, 99999));
    }
}
