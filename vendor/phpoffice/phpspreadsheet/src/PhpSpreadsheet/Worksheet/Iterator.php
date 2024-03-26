<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Iterator implements \Iterator
{
    
    private $subject;

    
    private $position = 0;

    
    public function __construct(Spreadsheet $subject)
    {
        
        $this->subject = $subject;
    }

    
    public function __destruct()
    {
        $this->subject = null;
    }

    
    public function rewind(): void
    {
        $this->position = 0;
    }

    
    public function current()
    {
        return $this->subject->getSheet($this->position);
    }

    
    public function key()
    {
        return $this->position;
    }

    
    public function next(): void
    {
        ++$this->position;
    }

    
    public function valid()
    {
        return $this->position < $this->subject->getSheetCount() && $this->position >= 0;
    }
}
