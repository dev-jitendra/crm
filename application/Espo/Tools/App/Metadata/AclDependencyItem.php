<?php


namespace Espo\Tools\App\Metadata;

class AclDependencyItem
{
    public function __construct(
        private string $target,
        private string $scope,
        private ?string $field
    ) {}

    
    public function getTarget(): string
    {
        return $this->target;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}
