<?php


namespace Espo\Core\Formula\Parser\Ast;


class Value
{
    public function __construct(private mixed $value)
    {}

    public function getValue(): mixed
    {
        return $this->value;
    }
}
