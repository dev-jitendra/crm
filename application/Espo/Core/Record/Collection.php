<?php


namespace Espo\Core\Record;

use Espo\ORM\Collection as OrmCollection;
use Espo\ORM\EntityCollection;

use stdClass;


class Collection
{
    public const TOTAL_HAS_MORE = -1;
    public const TOTAL_HAS_NO_MORE = -2;

    
    public function __construct(
        private OrmCollection $collection,
        private ?int $total = null
    ) {}

    
    public function getTotal(): ?int
    {
        return $this->total;
    }

    
    public function getCollection(): OrmCollection
    {
        return $this->collection;
    }

    
    public function getValueMapList(): array
    {
        if (
            $this->collection instanceof EntityCollection &&
            !$this->collection->getEntityType()
        ) {
            $list = [];

            foreach ($this->collection as $e) {
                $item = $e->getValueMap();

                $item->_scope = $e->getEntityType();

                $list[] = $item;
            }

            return $list;
        }

        return $this->collection->getValueMapList();
    }

    
    public static function create(OrmCollection $collection, ?int $total = null): self
    {
        return new self($collection, $total);
    }

    
    public static function createNoCount(OrmCollection $collection, ?int $maxSize): self
    {
        if (
            $maxSize !== null &&
            $collection instanceof EntityCollection &&
            count($collection) > $maxSize
        ) {
            $copyCollection = new EntityCollection([...$collection], $collection->getEntityType());

            unset($copyCollection[count($copyCollection) - 1]);

            return new self($copyCollection, self::TOTAL_HAS_MORE);
        }

        return new self($collection, self::TOTAL_HAS_NO_MORE);
    }
}
