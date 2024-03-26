<?php


namespace Espo\Tools\Layout;

class LayoutDefs
{
    
    public function __construct(
        private string $scope,
        private string $name,
        private string $type,
        private string $label
    ) {}

    
    public function getScope(): string
    {
        return $this->scope;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getType(): string
    {
        return $this->type;
    }

    
    public function getLabel(): string
    {
        return $this->label;
    }
}
