<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\WhereItem;


class Not implements WhereItem
{
    
    private $rawValue = [];

    public function getRaw(): array
    {
        return ['NOT' => $this->getRawValue()];
    }

    public function getRawKey(): string
    {
        return 'NOT';
    }

    
    public function getRawValue(): array
    {
        return $this->rawValue;
    }

    
    public static function fromRaw(array $whereClause): self
    {
        if (count($whereClause) === 1 && array_keys($whereClause)[0] === 0) {
            $whereClause = $whereClause[0];
        }

        $obj = new self();

        $obj->rawValue = $whereClause;

        return $obj;
    }

    public static function create(WhereItem $item): self
    {
        return self::fromRaw($item->getRaw());
    }
}
