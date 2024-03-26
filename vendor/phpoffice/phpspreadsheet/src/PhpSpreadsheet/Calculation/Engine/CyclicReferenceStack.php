<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

class CyclicReferenceStack
{
    
    private $stack = [];

    
    public function count()
    {
        return count($this->stack);
    }

    
    public function push($value): void
    {
        $this->stack[$value] = $value;
    }

    
    public function pop()
    {
        return array_pop($this->stack);
    }

    
    public function onStack($value)
    {
        return isset($this->stack[$value]);
    }

    
    public function clear(): void
    {
        $this->stack = [];
    }

    
    public function showStack()
    {
        return $this->stack;
    }
}
