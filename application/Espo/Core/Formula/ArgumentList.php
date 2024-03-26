<?php


namespace Espo\Core\Formula;

use BadMethodCallException;
use OutOfBoundsException;
use Iterator;
use Countable;
use ArrayAccess;
use SeekableIterator;


class ArgumentList implements Evaluatable, Iterator, Countable, ArrayAccess, SeekableIterator
{
    private int $position = 0;

    
    public function __construct(private array $dataList)
    {}

    private function getLastValidKey(): int
    {
        $keys = array_keys($this->dataList);

        $i = end($keys);

        if ($i === false) {
            return -1;
        }

        while ($i > 0) {
            if (array_key_exists($i, $this->dataList)) {
                break;
            }

            $i--;
        }

        return $i;
    }

    public function rewind(): void
    {
        $this->position = 0;

        while (!$this->valid() && $this->position <= $this->getLastValidKey()) {
            $this->position ++;
        }
    }

    private function getArgumentByIndex(int $index): Argument
    {
        return new Argument($this->dataList[$index]);
    }

    
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->getArgumentByIndex($this->position);
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

            if (
                !$this->valid() &&
                $this->position <= $this->getLastValidKey()
            ) {
                $next = true;
            }
        } while ($next);
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->dataList);
    }

    
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->dataList);
    }

    
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        return $this->getArgumentByIndex($offset);
    }

    
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Setting is not allowed.');
    }

    
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Unsetting is not allowed.');
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
}
