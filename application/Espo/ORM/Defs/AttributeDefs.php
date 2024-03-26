<?php


namespace Espo\ORM\Defs;


class AttributeDefs
{
    
    private array $data;
    private string $name;

    private function __construct()
    {}

    
    public static function fromRaw(array $raw, string $name): self
    {
        $obj = new self();
        $obj->data = $raw;
        $obj->name = $name;

        return $obj;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getType(): string
    {
        return $this->data['type'];
    }

    
    public function getLength(): ?int
    {
        return $this->data['len'] ?? null;
    }

    
    public function isNotStorable(): bool
    {
        return $this->data['notStorable'] ?? false;
    }

    
    public function isAutoincrement(): bool
    {
        return $this->data['autoincrement'] ?? false;
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    
    public function getParam(string $name)
    {
        return $this->data[$name] ?? null;
    }
}
