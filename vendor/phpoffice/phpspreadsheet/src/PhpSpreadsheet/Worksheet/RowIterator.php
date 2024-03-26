<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class RowIterator implements Iterator
{
    
    private $subject;

    
    private $position = 1;

    
    private $startRow = 1;

    
    private $endRow = 1;

    
    public function __construct(Worksheet $subject, $startRow = 1, $endRow = null)
    {
        
        $this->subject = $subject;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    
    public function __destruct()
    {
        $this->subject = null;
    }

    
    public function resetStart($startRow = 1)
    {
        if ($startRow > $this->subject->getHighestRow()) {
            throw new PhpSpreadsheetException("Start row ({$startRow}) is beyond highest row ({$this->subject->getHighestRow()})");
        }

        $this->startRow = $startRow;
        if ($this->endRow < $this->startRow) {
            $this->endRow = $this->startRow;
        }
        $this->seek($startRow);

        return $this;
    }

    
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->subject->getHighestRow();

        return $this;
    }

    
    public function seek($row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new PhpSpreadsheetException("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        }
        $this->position = $row;

        return $this;
    }

    
    public function rewind(): void
    {
        $this->position = $this->startRow;
    }

    
    public function current()
    {
        return new Row($this->subject, $this->position);
    }

    
    public function key()
    {
        return $this->position;
    }

    
    public function next(): void
    {
        ++$this->position;
    }

    
    public function prev(): void
    {
        --$this->position;
    }

    
    public function valid()
    {
        return $this->position <= $this->endRow && $this->position >= $this->startRow;
    }
}
