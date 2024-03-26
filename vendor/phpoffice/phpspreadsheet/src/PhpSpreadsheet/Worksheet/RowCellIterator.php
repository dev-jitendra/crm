<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class RowCellIterator extends CellIterator
{
    
    private $currentColumnIndex;

    
    private $rowIndex = 1;

    
    private $startColumnIndex = 1;

    
    private $endColumnIndex = 1;

    
    public function __construct(?Worksheet $worksheet = null, $rowIndex = 1, $startColumn = 'A', $endColumn = null)
    {
        
        $this->worksheet = $worksheet;
        $this->rowIndex = $rowIndex;
        $this->resetEnd($endColumn);
        $this->resetStart($startColumn);
    }

    
    public function resetStart($startColumn = 'A')
    {
        $this->startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        $this->adjustForExistingOnlyRange();
        $this->seek(Coordinate::stringFromColumnIndex($this->startColumnIndex));

        return $this;
    }

    
    public function resetEnd($endColumn = null)
    {
        $endColumn = $endColumn ? $endColumn : $this->worksheet->getHighestColumn();
        $this->endColumnIndex = Coordinate::columnIndexFromString($endColumn);
        $this->adjustForExistingOnlyRange();

        return $this;
    }

    
    public function seek($column = 'A')
    {
        $columnx = $column;
        $column = Coordinate::columnIndexFromString($column);
        if ($this->onlyExistingCells && !($this->worksheet->cellExistsByColumnAndRow($column, $this->rowIndex))) {
            throw new PhpSpreadsheetException('In "IterateOnlyExistingCells" mode and Cell does not exist');
        }
        if (($column < $this->startColumnIndex) || ($column > $this->endColumnIndex)) {
            throw new PhpSpreadsheetException("Column $columnx is out of range ({$this->startColumnIndex} - {$this->endColumnIndex})");
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
        return $this->worksheet->getCellByColumnAndRow($this->currentColumnIndex, $this->rowIndex);
    }

    
    public function key()
    {
        return Coordinate::stringFromColumnIndex($this->currentColumnIndex);
    }

    
    public function next(): void
    {
        do {
            ++$this->currentColumnIndex;
        } while (($this->onlyExistingCells) && (!$this->worksheet->cellExistsByColumnAndRow($this->currentColumnIndex, $this->rowIndex)) && ($this->currentColumnIndex <= $this->endColumnIndex));
    }

    
    public function prev(): void
    {
        do {
            --$this->currentColumnIndex;
        } while (($this->onlyExistingCells) && (!$this->worksheet->cellExistsByColumnAndRow($this->currentColumnIndex, $this->rowIndex)) && ($this->currentColumnIndex >= $this->startColumnIndex));
    }

    
    public function valid()
    {
        return $this->currentColumnIndex <= $this->endColumnIndex && $this->currentColumnIndex >= $this->startColumnIndex;
    }

    
    public function getCurrentColumnIndex()
    {
        return $this->currentColumnIndex;
    }

    
    protected function adjustForExistingOnlyRange(): void
    {
        if ($this->onlyExistingCells) {
            while ((!$this->worksheet->cellExistsByColumnAndRow($this->startColumnIndex, $this->rowIndex)) && ($this->startColumnIndex <= $this->endColumnIndex)) {
                ++$this->startColumnIndex;
            }
            while ((!$this->worksheet->cellExistsByColumnAndRow($this->endColumnIndex, $this->rowIndex)) && ($this->endColumnIndex >= $this->startColumnIndex)) {
                --$this->endColumnIndex;
            }
        }
    }
}
