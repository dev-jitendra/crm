<?php


namespace Espo\Core\Field;

use RuntimeException;


class EmailAddressGroup
{
    
    private array $list = [];
    private ?EmailAddress $primary = null;

    
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

    
    public function getPrimaryAddress(): ?string
    {
        $primary = $this->getPrimary();

        if (!$primary) {
            return null;
        }

        return $primary->getAddress();
    }

    
    public function getPrimary(): ?EmailAddress
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

    
    public function getAddressList(): array
    {
        $list = [];

        foreach ($this->list as $item) {
            $list[] = $item->getAddress();
        }

        return $list;
    }

    
    public function getByAddress(string $address): ?EmailAddress
    {
        $index = $this->searchAddressInList($address);

        if ($index === null) {
            return null;
        }

        return $this->list[$index];
    }

    
    public function hasAddress(string $address): bool
    {
        return in_array($address, $this->getAddressList());
    }

    
    public function withPrimary(EmailAddress $emailAddress): self
    {
        $list = $this->list;

        $index = $this->searchAddressInList($emailAddress->getAddress());

        if ($index !== null) {
            unset($list[$index]);

            $list = array_values($list);
        }

        $newList = array_merge([$emailAddress], $list);

        return self::create($newList);
    }

    
    public function withAddedList(array $list): self
    {
        $newList = $this->list;

        foreach ($list as $item) {
            $index = $this->searchAddressInList($item->getAddress());

            if ($index !== null) {
                $newList[$index] = $item;

                continue;
            }

            $newList[] = $item;
        }

        return self::create($newList);
    }

    
    public function withAdded(EmailAddress $emailAddress): self
    {
        return $this->withAddedList([$emailAddress]);
    }

    
    public function withRemoved(EmailAddress $emailAddress): self
    {
        return $this->withRemovedByAddress($emailAddress->getAddress());
    }

    
    public function withRemovedByAddress(string $address): self
    {
        $newList = $this->list;

        $index = $this->searchAddressInList($address);

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

    private function searchAddressInList(string $address): ?int
    {
        foreach ($this->getAddressList() as $i => $item) {
            if ($item === $address) {
                return $i;
            }
        }

        return null;
    }

    private function validateList(): void
    {
        $addressList = [];

        foreach ($this->list as $item) {
            if (!$item instanceof EmailAddress) {
                throw new RuntimeException("Bad item.");
            }

            if (in_array(strtolower($item->getAddress()), $addressList)) {
                throw new RuntimeException("Address list contains a duplicate.");
            }

            $addressList[] = strtolower($item->getAddress());
        }
    }

    private function isEmpty(): bool
    {
        return count($this->list) === 0;
    }
}
