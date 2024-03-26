<?php


namespace Espo\Core\Record;


class UpdateParams
{
    private bool $skipDuplicateCheck = false;
    private ?int $versionNumber = null;

    public function __construct() {}

    public function withSkipDuplicateCheck(bool $skipDuplicateCheck = true): self
    {
        $obj = clone $this;
        $obj->skipDuplicateCheck = $skipDuplicateCheck;

        return $obj;
    }

    public function withVersionNumber(?int $versionNumber): self
    {
        $obj = clone $this;
        $obj->versionNumber = $versionNumber;

        return $obj;
    }

    public function skipDuplicateCheck(): bool
    {
        return $this->skipDuplicateCheck;
    }

    public function getVersionNumber(): ?int
    {
        return $this->versionNumber;
    }

    public static function create(): self
    {
        return new self();
    }
}
