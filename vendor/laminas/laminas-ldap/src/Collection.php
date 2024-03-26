<?php

namespace Laminas\Ldap;

use Countable;
use Iterator;
use ReturnTypeWillChange;

use function array_key_exists;


class Collection implements Iterator, Countable
{
    
    protected $iterator;

    
    protected $current = -1;

    
    protected $cache = [];

    public function __construct(Collection\DefaultIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function __destruct()
    {
        $this->close();
    }

    
    public function close()
    {
        return $this->iterator->close();
    }

    
    public function toArray()
    {
        $data = [];
        foreach ($this as $item) {
            $data[] = $item;
        }
        return $data;
    }

    
    public function getFirst()
    {
        if ($this->count() < 1) {
            return null;
        }

        $this->rewind();
        return $this->current();
    }

    
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->iterator->count();
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($this->count() < 1) {
            return null;
        }

        if ($this->current < 0) {
            $this->rewind();
        }

        if (! array_key_exists($this->current, $this->cache)) {
            $current = $this->iterator->current();
            if ($current === null) {
                return null;
            }
            $this->cache[$this->current] = $this->createEntry($current);
        }

        return $this->cache[$this->current];
    }

    
    protected function createEntry(array $data)
    {
        return $data;
    }

    
    public function dn()
    {
        if ($this->count() > 0) {
            if ($this->current < 0) {
                $this->rewind();
            }
            return $this->iterator->key();
        }
        return null;
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        if ($this->count() > 0) {
            if ($this->current < 0) {
                $this->rewind();
            }
            return $this->current;
        }
        return null;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->iterator->next();
        $this->current++;
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->iterator->rewind();
        $this->current = 0;
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        if (isset($this->cache[$this->current])) {
            return true;
        }
        return $this->iterator->valid();
    }
}
