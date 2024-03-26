<?php


namespace Espo\Tools\Export\Processor;

use RuntimeException;


class Params
{
    private string $fileName;
    
    private array $attributeList;
    
    private ?array $fieldList = null;
    private ?string $name = null;
    private ?string $entityType = null;
    
    private array $params = [];

    
    public function __construct(string $fileName, array $attributeList, ?array $fieldList)
    {
        $this->fileName = $fileName;
        $this->attributeList = $attributeList;
        $this->fieldList = $fieldList;
    }

    public function withEntityType(string $entityType): self
    {
        $obj = clone $this;
        $obj->entityType = $entityType;

        return $obj;
    }

    public function withName(?string $name): self
    {
        $obj = clone $this;
        $obj->name = $name;

        return $obj;
    }

    
    public function withFieldList(?array $fieldList): self
    {
        $obj = clone $this;
        $obj->fieldList = $fieldList;

        return $obj;
    }

    
    public function withAttributeList(array $attributeList): self
    {
        $obj = clone $this;
        $obj->attributeList = $attributeList;

        return $obj;
    }

    public function withParam(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->params[$name] = $value;

        return $obj;
    }

    
    public function getFileName(): string
    {
        return $this->fileName;
    }

    
    public function getAttributeList(): array
    {
        return $this->attributeList;
    }

    
    public function getFieldList(): ?array
    {
        return $this->fieldList;
    }

    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function getEntityType(): string
    {
        if ($this->entityType === null) {
            throw new RuntimeException("No entity-type.");
        }

        return $this->entityType;
    }

    
    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}
