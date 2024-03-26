<?php


namespace Espo\ORM\Defs;


class IndexDefs
{
    
    private $data;
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

    
    public function getKey(): string
    {
        return $this->data['key'] ?? '';
    }

    
    public function isUnique(): bool
    {
        
        if (($this->data['unique'] ?? false)) {
            return true;
        }

        $type = $this->data['type'] ?? null;

        return $type === 'unique';
    }

    
    public function getColumnList(): array
    {
        return $this->data['columns'] ?? [];
    }

    
    public function getFlagList(): array
    {
        return $this->data['flags'] ?? [];
    }
}
