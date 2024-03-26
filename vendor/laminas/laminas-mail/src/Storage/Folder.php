<?php

namespace Laminas\Mail\Storage;

use RecursiveIterator;
use ReturnTypeWillChange;
use Stringable;

use function current;
use function key;
use function next;
use function reset;

class Folder implements RecursiveIterator, Stringable
{
    
    protected $globalName;

    
    public function __construct(
        protected $localName,
        $globalName = '',
        protected $selectable = true,
        protected array $folders = []
    ) {
        $this->globalName = $globalName ?: $localName;
    }

    
    #[ReturnTypeWillChange]
    public function hasChildren()
    {
        $current = $this->current();
        return $current && $current instanceof self && ! $current->isLeaf();
    }

    
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        return $this->current();
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return key($this->folders) !== null;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->folders);
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->folders);
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->folders);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->folders);
    }

    
    public function __get($name)
    {
        if (! isset($this->folders[$name])) {
            throw new Exception\InvalidArgumentException("no subfolder named $name");
        }

        return $this->folders[$name];
    }

    
    public function __set($name, self $folder)
    {
        $this->folders[$name] = $folder;
    }

    
    public function __unset($name)
    {
        unset($this->folders[$name]);
    }

    
    public function __toString(): string
    {
        return (string) $this->getGlobalName();
    }

    
    public function getLocalName()
    {
        return $this->localName;
    }

    
    public function getGlobalName()
    {
        return $this->globalName;
    }

    
    public function isSelectable()
    {
        return $this->selectable;
    }

    
    public function isLeaf()
    {
        return empty($this->folders);
    }
}
