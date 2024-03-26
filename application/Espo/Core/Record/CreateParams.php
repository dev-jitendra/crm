<?php


namespace Espo\Core\Record;


class CreateParams
{
    private bool $skipDuplicateCheck = false;
    private ?string $duplicateSourceId = null;

    public function __construct() {}

    public function withSkipDuplicateCheck(bool $skipDuplicateCheck = true): self
    {
        $obj = clone $this;

        $obj->skipDuplicateCheck = $skipDuplicateCheck;

        return $obj;
    }

    public function withDuplicateSourceId(?string $duplicateSourceId): self
    {
        $obj = clone $this;

        $obj->duplicateSourceId = $duplicateSourceId;

        return $obj;
    }

    public function skipDuplicateCheck(): bool
    {
        return $this->skipDuplicateCheck;
    }

    public function getDuplicateSourceId(): ?string
    {
        return $this->duplicateSourceId;
    }

    public static function create(): self
    {
        return new self();
    }
}
