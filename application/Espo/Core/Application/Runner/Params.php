<?php


namespace Espo\Core\Application\Runner;


class Params
{
    
    private $data = [];

    public function __construct() {}

    
    public function get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    
    public function with(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->data[$name] = $value;

        return $obj;
    }

    
    public static function fromArray(array $data): self
    {
        $obj = new self();
        $obj->data = $data;

        return $obj;
    }

    
    public static function create(): self
    {
        return new self();
    }
}
