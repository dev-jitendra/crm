<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\WhereItem;


class AndGroup implements WhereItem
{
    
    private $rawValue = [];

    
    public function getRaw(): array
    {
        return ['AND' => $this->getRawValue()];
    }

    public function getRawKey(): string
    {
        return 'AND';
    }

    
    public function getRawValue(): array
    {
        return $this->rawValue;
    }

    
    public function getItemCount(): int
    {
        return count($this->rawValue);
    }

    
    public static function fromRaw(array $whereClause): self
    {
        if (count($whereClause) === 1 && array_keys($whereClause)[0] === 0) {
            $whereClause = $whereClause[0];
        }

        
        $obj = static::class === WhereClause::class ?
            new WhereClause() :
            new self();

        $obj->rawValue = $whereClause;

        return $obj;
    }

    public static function create(WhereItem ...$itemList): self
    {
        $builder = self::createBuilder();

        foreach ($itemList as $item) {
            $builder->add($item);
        }

        return $builder->build();
    }

    public static function createBuilder(): AndGroupBuilder
    {
        return new AndGroupBuilder();
    }
}
