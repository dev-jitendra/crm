<?php


namespace Espo\Tools\App\Language;

class AclDependencyItem
{
    
    public function __construct(
        private string $target,
        private ?array $anyScopeList,
        private ?string $scope,
        private ?string $field
    ) {}

    
    public function getTarget(): string
    {
        return $this->target;
    }

    
    public function getAnyScopeList(): ?array
    {
        return $this->anyScopeList;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}
