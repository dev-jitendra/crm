<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class ColumnIterator implements Iterator
{
    
    private $worksheet;

    
    private $currentColumnIndex = 1;

    
    private $startColumnIndex = 1;

    
    private $endColumnIndex = 1;

    
    public function __construct(Worksheet $worksheet, $startColumn = 'A', $endColumn = null)
    {
        
        $this->worksheet = $worksheet;
        $this->resetEnd($endColumn);
        $this->resetStart($startColumn);
    }

    
    public function __destruct()
    {
        $this->worksheet = null;
    }

    
    public function resetStart($startColumn = 'A')
    {
        $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        if ($startColumnIndex > Coordinate::columnIndexFromString($this->worksheet->getHighestColumn())) {
            throw new Exception("Start column ({$startColumn}) is beyond highest column ({$this->worksheet->getHighestColumn()})");
        }

        $this->startColumnIndex = $startColumnIndex;
        if ($this->endColumnIndex < $this->startColumnIndex) {
            $this->endColumnIndex = $this->startColumnIndex;
        }
        $this->seek($startColumn);

        return $this;
    }

    
    public function resetEnd($endColumn = null)
    {
        $endColumn = $endColumn ? $endColumn : $this->worksheet->getHighestColumn();
        $this->endColumnIndex = Coordinate::columnIndexFromString($endColumn);

        return $this;
    }

    
    public function seek($column = 'A')
    {
        $column = Coordinate::columnIndexFromString($column);
        if (($column < $this->startColumnIndex) || ($column > $this->endColumnIndex)) {
            throw new PhpSpreadsheetException("Column $column is out of range ({$this->startColumnIndex} - {$this->endColumnIndex})");
        }
        $this->currentColumnIndex = $column;

        return $this;
    }

    
    public function rewind(): void
    {
        $this->currentColumnIndex = $this->startColumnIndex;
    }

    
    public function current()
    {
        return new Column($this->worksheet, Coordinate::stringFromColumnIndex($this->currentColumnIndex));
    }

    
    public function key()
    {
        return Coordinate::stringFromColumnIndex($this->currentColumnIndex);
    }

    
    public function next(): void
    {
        ++$this->currentColumnIndex;
    }

    
    public function prev(): void
    {
        --$this->currentColumnIndex;
    }

    
    public function valid()
    {
        return $this->currentColumnIndex <= $this->endColumnIndex && $this->currentColumnIndex >= $this->startColumnIndex;
    }
}
