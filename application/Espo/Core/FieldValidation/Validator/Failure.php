<?php


namespace Espo\Core\FieldValidation\Validator;

class Failure
{
    private function __construct()
    {}

    public static function create(): self
    {
        return new self();
    }
}
