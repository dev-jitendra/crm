<?php


namespace Espo\Core\FieldValidation;

class FieldValidationParams
{
    
    private $skipFieldList = [];
    
    private $typeSkipFieldListData = [];

    public function __construct() {}

    
    public function getSkipFieldList(): array
    {
        return $this->skipFieldList;
    }

    
    public function getTypeSkipFieldList(string $type): array
    {
        return $this->typeSkipFieldListData[$type] ?? [];
    }

    
    public function withSkipFieldList(array $list): self
    {
        $obj = clone $this;
        $obj->skipFieldList = $list;

        return $obj;
    }

    
    public function withTypeSkipFieldList(string $type, array $list): self
    {
        $obj = clone $this;
        $obj->typeSkipFieldListData[$type] = $list;

        return $obj;
    }

    
    public static function create(): self
    {
        return new self();
    }
}
