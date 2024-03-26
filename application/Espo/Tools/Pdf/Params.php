<?php


namespace Espo\Tools\Pdf;


class Params
{
    private bool $applyAcl = false;

    public function applyAcl(): bool
    {
        return $this->applyAcl;
    }

    public function withAcl(bool $applyAcl = true): self
    {
        $obj = clone $this;

        $obj->applyAcl = $applyAcl;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
