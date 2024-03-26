<?php


namespace Espo\ORM\Query\Part\Where;

use Espo\ORM\Query\Part\WhereItem;


class OrGroup implements WhereItem
{

    
    private $rawValue = [];

    public function __construct()
    {
    }

    public function getRaw(): array
    {
        return ['OR' => $this->rawValue];
    }

    public function getRawKey(): string
    {
        return 'OR';
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
        $obj = new self();

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

    public static function createBuilder(): OrGroupBuilder
    {
        return new OrGroupBuilder();
    }
}
