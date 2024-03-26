<?php


namespace Espo\Core\Utils\Resource\FileReader;


class Params
{
    private ?string $scope = null;
    private ?string $moduleName = null;

    public static function create(): self
    {
        return new self();
    }

    public function withScope(?string $scope): self
    {
        $obj = clone $this;
        $obj->scope = $scope;

        return $obj;
    }

    public function withModuleName(?string $moduleName): self
    {
        $obj = clone $this;
        $obj->moduleName = $moduleName;

        return $obj;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }
}
