<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Select;


class Exists implements WhereItem
{
    private function __construct(private Select $rawValue) {}

    public function getRaw(): array
    {
        return ['EXISTS' => $this->getRawValue()];
    }

    public function getRawKey(): string
    {
        return 'EXISTS';
    }

    public function getRawValue(): Select
    {
        return $this->rawValue;
    }

    public static function create(Select $subQuery): self
    {
        return new self($subQuery);
    }
}
