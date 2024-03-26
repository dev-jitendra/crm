<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator;

abstract class CellIterator implements Iterator
{
    
    protected $worksheet;

    
    protected $onlyExistingCells = false;

    
    public function __destruct()
    {
        $this->worksheet = null;
    }

    
    public function getIterateOnlyExistingCells()
    {
        return $this->onlyExistingCells;
    }

    
    abstract protected function adjustForExistingOnlyRange();

    
    public function setIterateOnlyExistingCells($value): void
    {
        $this->onlyExistingCells = (bool) $value;

        $this->adjustForExistingOnlyRange();
    }
}
