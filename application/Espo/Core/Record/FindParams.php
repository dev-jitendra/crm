<?php


namespace Espo\Core\Record;


class FindParams
{
    private bool $noTotal = false;

    public function __construct() {}

    public function withNoTotal(bool $noTotal = true): self
    {
        $obj = clone $this;
        $obj->noTotal = $noTotal;

        return $obj;
    }

    public function noTotal(): bool
    {
        return $this->noTotal;
    }

    public static function create(): self
    {
        return new self();
    }
}
