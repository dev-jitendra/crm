<?php


namespace Espo\Tools\Export;


class ServiceResult
{
    private ?Result $result = null;
    private ?string $id = null;

    private function __construct() {}

    public function hasResult(): bool
    {
        return $this->result !== null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public static function createWithId(string $id): self
    {
        $obj = new self;
        $obj->id = $id;

        return $obj;
    }

    public static function createWithResult(Result $result): self
    {
        $obj = new self;
        $obj->result = $result;

        return $obj;
    }
}
