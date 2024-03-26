<?php


namespace Espo\Core\FieldProcessing\Loader;


class Params
{
    
    private ?array $select = null;

    public function __construct() {}

    public function hasInSelect(string $field): bool
    {
        return $this->hasSelect() && in_array($field, $this->select ?? []);
    }

    public function hasSelect(): bool
    {
        return $this->select !== null;
    }

    
    public function getSelect(): ?array
    {
        return $this->select;
    }

    
    public function withSelect(?array $select): self
    {
        $obj = clone $this;
        $obj->select = $select;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
