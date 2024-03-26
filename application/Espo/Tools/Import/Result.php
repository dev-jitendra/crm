<?php


namespace Espo\Tools\Import;

use stdClass;


class Result
{
    private ?string $id = null;
    private int $countCreated = 0;
    private int $countUpdated = 0;
    private int $countError = 0;
    private int $countDuplicate = 0;
    private bool $manualMode = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCountCreated(): int
    {
        return $this->countCreated;
    }

    public function getCountUpdated(): int
    {
        return $this->countUpdated;
    }

    public function getCountError(): int
    {
        return $this->countError;
    }

    public function getCountDuplicate(): int
    {
        return $this->countDuplicate;
    }

    public function isManualMode(): bool
    {
        return $this->manualMode;
    }

    public static function create(): self
    {
        return new self();
    }

    public function withId(?string $id): self
    {
        $obj = clone $this;
        $obj->id = $id;

        return $obj;
    }

    public function withCountCreated(int $value): self
    {
        $obj = clone $this;
        $obj->countCreated = $value;

        return $obj;
    }

    public function withCountUpdated(int $value): self
    {
        $obj = clone $this;
        $obj->countUpdated = $value;

        return $obj;
    }

    public function withCountError(int $value): self
    {
        $obj = clone $this;
        $obj->countError = $value;

        return $obj;
    }

    public function withCountDuplicate(int $value): self
    {
        $obj = clone $this;
        $obj->countDuplicate = $value;

        return $obj;
    }

    public function withManualMode(bool $manualMode = true): self
    {
        $obj = clone $this;
        $obj->manualMode = $manualMode;

        return $obj;
    }

    public function getValueMap(): stdClass
    {
        return (object) [
            'id' => $this->id,
            'countCreated' => $this->countCreated,
            'countUpdated' => $this->countUpdated,
            'manualMode' => $this->manualMode,
        ];
    }
}
