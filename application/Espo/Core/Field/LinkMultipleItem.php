<?php


namespace Espo\Core\Field;

use RuntimeException;


class LinkMultipleItem
{
    private string $id;

    private ?string $name = null;

    
    private array $columnData = [];

    
    public function __construct(string $id)
    {
        if ($id === '') {
            throw new RuntimeException("Empty ID.");
        }

        $this->id = $id;
    }

    
    public function getId(): string
    {
        return $this->id;
    }

    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function getColumnValue(string $column)
    {
        return $this->columnData[$column] ?? null;
    }

    
    public function hasColumnValue(string $column): bool
    {
        return array_key_exists($column, $this->columnData);
    }

    
    public function getColumnList(): array
    {
        return array_keys($this->columnData);
    }

    
    public function withName(string $name): self
    {
        $obj = $this->clone();

        $obj->name = $name;

        return $obj;
    }

    
    public function withColumnValue(string $column, $value): self
    {
        $obj = $this->clone();

        $obj->columnData[$column] = $value;

        return $obj;
    }

    
    public static function create(string $id): self
    {
        return new self($id);
    }

    private function clone(): self
    {
        $obj = new self($this->id);

        $obj->name = $this->name;
        $obj->columnData = $this->columnData;

        return $obj;
    }
}
