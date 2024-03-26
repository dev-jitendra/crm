<?php


namespace Espo\Core\Field;

use RuntimeException;


class LinkMultiple
{
    
    public function __construct(private array $list = [])
    {
        $this->validateList();
    }

    public function __clone()
    {
        $newList = [];

        foreach ($this->list as $item) {
            $newList[] = clone $item;
        }

        $this->list = $newList;
    }

    
    public function hasId(string $id): bool
    {
        return $this->searchIdInList($id) !== null;
    }

    
    public function getIdList(): array
    {
        $idList = [];

        foreach ($this->list as $item) {
            $idList[] = $item->getId();
        }

        return $idList;
    }

    
    public function getList(): array
    {
        return $this->list;
    }

    
    public function getCount(): int
    {
        return count($this->list);
    }

    
    public function getById(string $id): ?LinkMultipleItem
    {
        foreach ($this->list as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }

        return null;
    }

    
    public function withAddedId(string $id): self
    {
        return $this->withAdded(LinkMultipleItem::create($id));
    }

    
    public function withAddedIdList(array $idList): self
    {
        $obj = $this;

        foreach ($idList as $id) {
            $obj = $obj->withAddedId($id);
        }

        return $obj;
    }

    
    public function withAdded(LinkMultipleItem $item): self
    {
        return $this->withAddedList([$item]);
    }

    
    public function withAddedList(array $list): self
    {
        $newList = $this->list;

        foreach ($list as $item) {
            $index = $this->searchIdInList($item->getId());

            if ($index !== null) {
                $newList[$index] = $item;

                continue;
            }

            $newList[] = $item;
        }

        return self::create($newList);
    }

    
    public function withRemoved(LinkMultipleItem $item): self
    {
        return $this->withRemovedById($item->getId());
    }

    
    public function withRemovedById(string $id): self
    {
        $newList = $this->list;

        $index = $this->searchIdInList($id);

        if ($index !== null) {
            unset($newList[$index]);

            $newList = array_values($newList);
        }

        return self::create($newList);
    }

    
    public static function create(array $list = []): self
    {
        return new self($list);
    }

    private function validateList(): void
    {
        $idList = [];

        foreach ($this->list as $item) {
            if (!$item instanceof LinkMultipleItem) {
                throw new RuntimeException("Bad item.");
            }

            if (in_array($item->getId(), $idList)) {
                throw new RuntimeException("List contains duplicates.");
            }

            $idList[] = strtolower($item->getId());
        }
    }

    private function searchIdInList(string $id): ?int
    {
        foreach ($this->getIdList() as $i => $item) {
            if ($item === $id) {
                return $i;
            }
        }

        return null;
    }
}
