<?php


namespace Espo\ORM\Defs;

use RuntimeException;


class FieldDefs
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
        $type = $this->data['type'] ?? null;

        if ($type === null) {
            throw new RuntimeException("Field '{$this->name}' has no type.");
        }

        return $type;
    }

    
    public function isNotStorable(): bool
    {
        return $this->data['notStorable'] ?? false;
    }

    
    public function getParam(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }
}
