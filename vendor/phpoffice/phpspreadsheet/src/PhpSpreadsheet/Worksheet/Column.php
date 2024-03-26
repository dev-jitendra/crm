<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Column
{
    
    private $parent;

    
    private $columnIndex;

    
    public function __construct(?Worksheet $parent = null, $columnIndex = 'A')
    {
        
        $this->parent = $parent;
        $this->columnIndex = $columnIndex;
    }

    
    public function __destruct()
    {
        $this->parent = null;
    }

    
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    
    public function getCellIterator($startRow = 1, $endRow = null)
    {
        return new ColumnCellIterator($this->parent, $this->columnIndex, $startRow, $endRow);
    }
}
