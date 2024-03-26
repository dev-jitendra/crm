<?php


namespace Espo\Core\Formula\Parser\Ast;


class Attribute
{
    public function __construct(private string $name)
    {}

    public function getName(): string
    {
        return $this->name;
    }
}
