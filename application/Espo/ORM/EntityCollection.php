<?php


namespace Espo\ORM;

use Iterator;
use Countable;
use ArrayAccess;
use SeekableIterator;
use RuntimeException;
use OutOfBoundsException;
use InvalidArgumentException;
use stdClass;


class EntityCollection implements Collection, Iterator, Countable, ArrayAccess, SeekableIterator
{
    private ?EntityFactory $entityFactory = null;
    private ?string $entityType;
    private int $position = 0;
    private bool $isFetched = false;
    
    protected array $dataList = [];

    
    public function __construct(
        array $dataList = [],
        ?string $entityType = null,
        ?EntityFactory $entityFactory = null
    ) {
        $this->dataList = $dataList;
        $this->entityType = $entityType;
        $this->entityFactory = $entityFactory;
    }

    public function rewind(): void
    {
        $this->position = 0;

        while (!$this->valid() && $this->position <= $this->getLastValidKey()) {
            $this->position ++;
        }
    }

    
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->getEntityByOffset($this->position);
    }

    
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        do {
            $this->position ++;

            $next = false;

            if (!$this->valid() && $this->position <= $this->getLastValidKey()) {
                $next = true;
            }
        } while ($next);
    }

    
    private function getLastValidKey()
    {
        $keys = array_keys($this->dataList);

        $i = end($keys);

        while ($i > 0) {
            if (isset($this->dataList[$i])) {
                break;
            }

            $i--;
        }

        return $i;
    }

    public function valid(): bool
    {
        return isset($this->dataList[$this->position]);
    }

    
    public function offsetExists($offset): bool
    {
        return isset($this->dataList[$offset]);
    }

    
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!isset($this->dataList[$offset])) {
            return null;
        }

        return $this->getEntityByOffset($offset);
    }

    
    public function offsetSet($offset, $value): void
    {
        if (!($value instanceof Entity)) {
            throw new InvalidArgumentException('Only Entity is allowed to be added to EntityCollection.');
        }

        

        if (is_null($offset)) {
            $this->dataList[] = $value;

            return;
        }

        $this->dataList[$offset] = $value;
    }

    
    public function offsetUnset($offset): void
    {
        unset($this->dataList[$offset]);
    }

    public function count(): int
    {
        return count($this->dataList);
    }

    
    public function seek($offset): void
    {
        $this->position = $offset;

        if (!$this->valid()) {
            throw new OutOfBoundsException("Invalid seek offset ($offset).");
        }
    }

    
    public function append(Entity $entity): void
    {
        $this->dataList[] = $entity;
    }

    
    private function getEntityByOffset($offset): Entity
    {
        if (!array_key_exists($offset, $this->dataList)) {
            throw new RuntimeException();
        }

        $value = $this->dataList[$offset];

        if ($value instanceof Entity) {
            
            return $value;
        }

        if (is_array($value)) {
            $this->dataList[$offset] = $this->buildEntityFromArray($value);

            return $this->dataList[$offset];
        }

        throw new RuntimeException();
    }

    
    protected function buildEntityFromArray(array $dataArray): Entity
    {
        if (!$this->entityFactory) {
            throw new RuntimeException("Can't build from array. EntityFactory was not passed to the constructor.");
        }

        assert($this->entityType !== null);

        
        $entity = $this->entityFactory->create($this->entityType);

        $entity->set($dataArray);

        if ($this->isFetched) {
            $entity->setAsFetched();
        }

        return $entity;
    }

    
    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    
    public function getDataList(): array
    {
        return $this->dataList;
    }

    
    public function merge(EntityCollection $collection): void
    {
        $incomingDataList = $collection->getDataList();

        foreach ($incomingDataList as $v) {
            if (!$this->contains($v)) {
                $this->dataList[] = $v;
            }
        }
    }

    
    public function contains($value): bool
    {
        if ($this->indexOf($value) !== false) {
            return true;
        }

        return false;
    }

    
    public function indexOf($value)
    {
        $index = 0;

        if (is_array($value)) {
            foreach ($this->dataList as $v) {
                if (is_array($v)) {
                    if ($value['id'] == $v['id']) {
                        return $index;
                    }
                }
                else if ($v instanceof Entity) {
                    if ($value['id'] == $v->getId()) {
                        return $index;
                    }
                }

                $index ++;
            }
        }
        else if ($value instanceof Entity) {
            foreach ($this->dataList as $v) {
                if (is_array($v)) {
                    if ($value->getId() == $v['id']) {
                        return $index;
                    }
                }
                else if ($v instanceof Entity) {
                    if ($value === $v) {
                        return $index;
                    }
                }

                $index ++;
            }
        }

        return false;
    }

    
    public function toArray(bool $itemsAsObjects = false): array
    {
        $arr = [];

        foreach ($this as $entity) {
            $item = $entity->getValueMap();

            if (!$itemsAsObjects) {
                $item = get_object_vars($item);
            }

            $arr[] = $item;
        }

        return $arr;
    }

    
    public function getValueMapList(): array
    {
        
        return $this->toArray(true);
    }

    
    public function setAsFetched(): void
    {
        $this->isFetched = true;
    }

    
    public function isFetched(): bool
    {
        return $this->isFetched;
    }

    
    public static function fromSthCollection(SthCollection $sthCollection): self
    {
        $entityList = [];

        foreach ($sthCollection as $entity) {
            $entityList[] = $entity;
        }

        
        $obj = new EntityCollection($entityList, $sthCollection->getEntityType());
        $obj->setAsFetched();

        return $obj;
    }
}
