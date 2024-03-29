<?php

namespace PhpOffice\PhpSpreadsheet;

class HashTable
{
    
    protected $items = [];

    
    protected $keyMap = [];

    
    public function __construct($pSource = null)
    {
        if ($pSource !== null) {
            
            $this->addFromSource($pSource);
        }
    }

    
    public function addFromSource(?array $pSource = null): void
    {
        
        if ($pSource == null) {
            return;
        }

        foreach ($pSource as $item) {
            $this->add($item);
        }
    }

    
    public function add(IComparable $pSource): void
    {
        $hash = $pSource->getHashCode();
        if (!isset($this->items[$hash])) {
            $this->items[$hash] = $pSource;
            $this->keyMap[count($this->items) - 1] = $hash;
        }
    }

    
    public function remove(IComparable $pSource): void
    {
        $hash = $pSource->getHashCode();
        if (isset($this->items[$hash])) {
            unset($this->items[$hash]);

            $deleteKey = -1;
            foreach ($this->keyMap as $key => $value) {
                if ($deleteKey >= 0) {
                    $this->keyMap[$key - 1] = $value;
                }

                if ($value == $hash) {
                    $deleteKey = $key;
                }
            }
            unset($this->keyMap[count($this->keyMap) - 1]);
        }
    }

    
    public function clear(): void
    {
        $this->items = [];
        $this->keyMap = [];
    }

    
    public function count()
    {
        return count($this->items);
    }

    
    public function getIndexForHashCode($pHashCode)
    {
        return array_search($pHashCode, $this->keyMap);
    }

    
    public function getByIndex($pIndex)
    {
        if (isset($this->keyMap[$pIndex])) {
            return $this->getByHashCode($this->keyMap[$pIndex]);
        }

        return null;
    }

    
    public function getByHashCode($pHashCode)
    {
        if (isset($this->items[$pHashCode])) {
            return $this->items[$pHashCode];
        }

        return null;
    }

    
    public function toArray()
    {
        return $this->items;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            }
        }
    }
}
