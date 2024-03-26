<?php


namespace Espo\Core\Binding\Key;


class NamedKey
{
    private function __construct(private string $parameterName)
    {}

    
    public static function create(string $parameterName): self
    {
        return new self($parameterName);
    }

    public function toString(): string
    {
        return '$' . $this->parameterName;
    }
}
