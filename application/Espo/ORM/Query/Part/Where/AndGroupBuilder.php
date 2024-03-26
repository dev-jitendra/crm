<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\WhereItem;

class AndGroupBuilder
{
    
    private array $raw = [];

    public function build(): AndGroup
    {
        return AndGroup::fromRaw($this->raw);
    }

    public function add(WhereItem $item): self
    {
        $key = $item->getRawKey();
        $value = $item->getRawValue();

        if ($item instanceof AndGroup) {
            $this->raw = self::normalizeRaw($this->raw);

            $this->raw[] = $item->getRawValue();

            return $this;
        }

        if (count($this->raw) === 0) {
            $this->raw[$key] = $value;

            return $this;
        }

        $this->raw = self::normalizeRaw($this->raw);

        $this->raw[] = [$key => $value];

        return $this;
    }

    
    public function merge(AndGroup $andGroup): self
    {
        $this->raw = array_merge(
            self::normalizeRaw($this->raw),
            self::normalizeRaw($andGroup->getRawValue())
        );

        return $this;
    }

    
    private static function normalizeRaw(array $raw): array
    {
        if (count($raw) === 1 && array_keys($raw)[0] !== 0) {
            return [$raw];
        }

        return $raw;
    }
}
