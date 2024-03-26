<?php


namespace Espo\Core\Select\Where\Item\Data;

use Espo\Core\Select\Where\Item\Data;

class DateTime implements Data
{
    private ?string $timeZone = null;

    public static function create(): self
    {
        return new self();
    }

    public function withTimeZone(?string $timeZone): self
    {
        $obj = clone $this;
        $obj->timeZone = $timeZone;

        return $obj;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }
}
