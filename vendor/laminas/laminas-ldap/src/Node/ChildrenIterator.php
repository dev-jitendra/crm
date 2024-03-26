<?php

namespace Laminas\Ldap\Node;

use ArrayAccess;
use Countable;
use Iterator;
use Laminas\Ldap;
use Laminas\Ldap\Node;
use RecursiveIterator;
use ReturnTypeWillChange;

use function array_key_exists;
use function count;
use function current;
use function key;
use function next;
use function reset;


class ChildrenIterator implements Iterator, Countable, RecursiveIterator, ArrayAccess
{
    
    private array $data;

    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->data);
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->data);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->data);
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return current($this->data) !== false;
    }

    
    #[ReturnTypeWillChange]
    public function hasChildren()
    {
        if ($this->current() instanceof Ldap\Node) {
            return $this->current()->hasChildren();
        }

        return false;
    }

    
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        if ($this->current() instanceof Ldap\Node) {
            return $this->current()->getChildren();
        }

        return null;
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        return null;
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
    }

    
    public function toArray()
    {
        $data = [];
        foreach ($this as $rdn => $node) {
            $data[$rdn] = $node;
        }
        return $data;
    }
}
