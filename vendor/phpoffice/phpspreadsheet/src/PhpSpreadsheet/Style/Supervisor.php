<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class Supervisor implements IComparable
{
    
    protected $isSupervisor;

    
    protected $parent;

    
    protected $parentPropertyName;

    
    public function __construct($isSupervisor = false)
    {
        
        $this->isSupervisor = $isSupervisor;
    }

    
    public function bindParent($parent, $parentPropertyName = null)
    {
        $this->parent = $parent;
        $this->parentPropertyName = $parentPropertyName;

        return $this;
    }

    
    public function getIsSupervisor()
    {
        return $this->isSupervisor;
    }

    
    public function getActiveSheet()
    {
        return $this->parent->getActiveSheet();
    }

    
    public function getSelectedCells()
    {
        return $this->getActiveSheet()->getSelectedCells();
    }

    
    public function getActiveCell()
    {
        return $this->getActiveSheet()->getActiveCell();
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != 'parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    
    final public function exportArray(): array
    {
        return $this->exportArray1();
    }

    
    abstract protected function exportArray1(): array;

    
    final protected function exportArray2(array &$exportedArray, string $index, $objOrValue): void
    {
        if ($objOrValue instanceof self) {
            $exportedArray[$index] = $objOrValue->exportArray();
        } else {
            $exportedArray[$index] = $objOrValue;
        }
    }
}
