<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Row
{
    
    private $worksheet;

    
    private $rowIndex = 0;

    
    public function __construct(?Worksheet $worksheet = null, $rowIndex = 1)
    {
        
        $this->worksheet = $worksheet;
        $this->rowIndex = $rowIndex;
    }

    
    public function __destruct()
    {
        $this->worksheet = null;
    }

    
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    
    public function getCellIterator($startColumn = 'A', $endColumn = null)
    {
        return new RowCellIterator($this->worksheet, $this->rowIndex, $startColumn, $endColumn);
    }

    
    public function getWorksheet()
    {
        return $this->worksheet;
    }
}
