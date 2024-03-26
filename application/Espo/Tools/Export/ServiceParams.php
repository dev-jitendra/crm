<?php


namespace Espo\Tools\Export;

class ServiceParams
{
    private bool $isIdle = false;

    public static function create(): self
    {
        return new self();
    }

    public function isIdle(): bool
    {
        return $this->isIdle;
    }

    public function withIsIdle(bool $isIdle = true): self
    {
        $obj = clone $this;

        $obj->isIdle = $isIdle;

        return $obj;
    }
}
