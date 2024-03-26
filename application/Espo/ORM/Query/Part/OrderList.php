<?php


namespace Espo\ORM\Query\Part;

use InvalidArgumentException;
use Iterator;


class OrderList implements Iterator
{
    private int $position = 0;
    
    private array $list;

    
    private function __construct(array $list)
    {
        foreach ($list as $item) {
            if (!$item instanceof Order) {
                throw new InvalidArgumentException();
            }
        }

        $this->list = $list;
    }

    
    public static function create(array $list): self
    {
        return new self($list);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Order
    {
        return $this->list[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->list[$this->position]);
    }
}
