<?php


namespace Espo\Core\Binding\Key;


class NamedClassKey
{
    
    private function __construct(private string $className, private string $parameterName)
    {}

    
    public static function create(string $className, string $parameterName): self
    {
        return new self($className, $parameterName);
    }

    public function toString(): string
    {
        return $this->className . ' $' . $this->parameterName;
    }
}
