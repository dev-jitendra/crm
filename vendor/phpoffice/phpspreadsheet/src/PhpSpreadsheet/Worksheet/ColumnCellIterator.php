<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class ColumnCellIterator extends CellIterator
{
    
    private $currentRow;

    
    private $columnIndex;

    
    private $startRow = 1;

    
    private $endRow = 1;

    
    public function __construct(?Worksheet $subject = null, $columnIndex = 'A', $startRow = 1, $endRow = null)
    {
        
        $this->worksheet = $subject;
        $this->columnIndex = Coordinate::columnIndexFromString($columnIndex);
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    
    public function resetStart($startRow = 1)
    {
        $this->startRow = $startRow;
        $this->adjustForExistingOnlyRange();
        $this->seek($startRow);

        return $this;
    }

    
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->worksheet->getHighestRow();
        $this->adjustForExistingOnlyRange();

        return $this;
    }

    
    public function seek($row = 1)
    {
        if ($this->onlyExistingCells && !($this->worksheet->cellExistsByColumnAndRow($this->columnIndex, $row))) {
            throw new PhpSpreadsheetException('In "IterateOnlyExistingCells" mode and Cell does not exist');
        }
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new PhpSpreadsheetException("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        }
        $this->currentRow = $row;

        return $this;
    }

    
    public function rewind(): void
    {
        $this->currentRow = $this->startRow;
    }

    
    public function current()
    {
        return $this->worksheet->getCellByColumnAndRow($this->columnIndex, $this->currentRow);
    }

    
    public function key()
    {
        return $this->currentRow;
    }

    
    public function next(): void
    {
        do {
            ++$this->currentRow;
        } while (
            ($this->onlyExistingCells) &&
            (!$this->worksheet->cellExistsByColumnAndRow($this->columnIndex, $this->currentRow)) &&
            ($this->currentRow <= $this->endRow)
        );
    }

    
    public function prev(): void
    {
        do {
            --$this->currentRow;
        } while (
            ($this->onlyExistingCells) &&
            (!$this->worksheet->cellExistsByColumnAndRow($this->columnIndex, $this->currentRow)) &&
            ($this->currentRow >= $this->startRow)
        );
    }

    
    public function valid()
    {
        return $this->currentRow <= $this->endRow && $this->currentRow >= $this->startRow;
    }

    
    protected function adjustForExistingOnlyRange(): void
    {
        if ($this->onlyExistingCells) {
            while (
                (!$this->worksheet->cellExistsByColumnAndRow($this->columnIndex, $this->startRow)) &&
                ($this->startRow <= $this->endRow)
            ) {
                ++$this->startRow;
            }
            while (
                (!$this->worksheet->cellExistsByColumnAndRow($this->columnIndex, $this->endRow)) &&
                ($this->endRow >= $this->startRow)
            ) {
                --$this->endRow;
            }
        }
    }
}
