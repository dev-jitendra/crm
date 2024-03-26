<?php


namespace Espo\Core\Utils\Resource\Reader;

class Params
{
    private bool $noCustom = false;
    
    private array $forceAppendPathList = [];

    public static function create(): self
    {
        return new self();
    }

    public function withNoCustom(bool $noCustom = true): self
    {
        $obj = clone $this;
        $obj->noCustom = $noCustom;

        return $obj;
    }

    
    public function withForceAppendPathList(array $forceAppendPathList): self
    {
        $obj = clone $this;
        $obj->forceAppendPathList = $forceAppendPathList;

        return $obj;
    }

    public function noCustom(): bool
    {
        return $this->noCustom;
    }

    
    public function getForceAppendPathList(): array
    {
        return $this->forceAppendPathList;
    }
}
