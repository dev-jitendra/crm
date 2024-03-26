<?php


namespace Espo\Core\Field;

use RuntimeException;


class PhoneNumberGroup
{
    
    private $list = [];
    private ?PhoneNumber $primary = null;

    
    public function __construct(array $list = [])
    {
        foreach ($list as $item) {
            $this->list[] = clone $item;
        }

        $this->validateList();

        if (count($this->list) !== 0) {
            $this->primary = $this->list[0];
        }
    }

    public function __clone()
    {
        $newList = [];

        foreach ($this->list as $item) {
            $newList[] = clone $item;
        }

        $this->list = $newList;

        if ($this->primary) {
            $this->primary = clone $this->primary;
        }
    }

    
    public function getPrimaryNumber(): ?string
    {
        $primary = $this->getPrimary();

        if (!$primary) {
            return null;
        }

        return $primary->getNumber();
    }

    
    public function getPrimary(): ?PhoneNumber
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->primary;
    }

    
    public function getList(): array
    {
        return $this->list;
    }

    
    public function getCount(): int
    {
        return count($this->list);
    }

    
    public function getSecondaryList(): array
    {
        $list = [];

        foreach ($this->list as $item) {
            if ($item === $this->primary) {
                continue;
            }

            $list[] = $item;
        }

        return $list;
    }

    
    public function getNumberList(): array
    {
        $list = [];

        foreach ($this->list as $item) {
            $list[] = $item->getNumber();
        }

        return $list;
    }

    
    public function getByNumber(string $number): ?PhoneNumber
    {
        $index = $this->searchNumberInList($number);

        if ($index === null) {
            return null;
        }

        return $this->list[$index];
    }

    
    public function hasNumber(string $number): bool
    {
        return in_array($number, $this->getNumberList());
    }

    
    public function withPrimary(PhoneNumber $phoneNumber): self
    {
        $list = $this->list;

        $index = $this->searchNumberInList($phoneNumber->getNumber());

        if ($index !== null) {
            unset($list[$index]);

            $list = array_values($list);
        }

        $newList = array_merge([$phoneNumber], $list);

        return self::create($newList);
    }

    
    public function withAddedList(array $list): self
    {
        $newList = $this->list;

        foreach ($list as $item) {
            $index = $this->searchNumberInList($item->getNumber());

            if ($index !== null) {
                $newList[$index] = $item;

                continue;
            }

            $newList[] = $item;
        }

        return self::create($newList);
    }

    
    public function withAdded(PhoneNumber $phoneNumber): self
    {
        return $this->withAddedList([$phoneNumber]);
    }

    
    public function withRemoved(PhoneNumber $phoneNumber): self
    {
        return $this->withRemovedByNumber($phoneNumber->getNumber());
    }

    
    public function withRemovedByNumber(string $number): self
    {
        $newList = $this->list;

        $index = $this->searchNumberInList($number);

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

    private function searchNumberInList(string $number): ?int
    {
        foreach ($this->getNumberList() as $i => $item) {
            if ($item === $number) {
                return $i;
            }
        }

        return null;
    }

    private function validateList(): void
    {
        $numberList = [];

        foreach ($this->list as $item) {
            if (!$item instanceof PhoneNumber) {
                throw new RuntimeException("Bad item.");
            }

            if (in_array($item->getNumber(), $numberList)) {
                throw new RuntimeException("Number list contains a duplicate.");
            }

            $numberList[] = strtolower($item->getNumber());
        }
    }

    private function isEmpty(): bool
    {
        return count($this->list) === 0;
    }
}
