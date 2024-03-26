<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Borders extends Supervisor
{
    
    const DIAGONAL_NONE = 0;
    const DIAGONAL_UP = 1;
    const DIAGONAL_DOWN = 2;
    const DIAGONAL_BOTH = 3;

    
    protected $left;

    
    protected $right;

    
    protected $top;

    
    protected $bottom;

    
    protected $diagonal;

    
    protected $diagonalDirection;

    
    protected $allBorders;

    
    protected $outline;

    
    protected $inside;

    
    protected $vertical;

    
    protected $horizontal;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        $this->left = new Border($isSupervisor, $isConditional);
        $this->right = new Border($isSupervisor, $isConditional);
        $this->top = new Border($isSupervisor, $isConditional);
        $this->bottom = new Border($isSupervisor, $isConditional);
        $this->diagonal = new Border($isSupervisor, $isConditional);
        $this->diagonalDirection = self::DIAGONAL_NONE;

        
        if ($isSupervisor) {
            
            $this->allBorders = new Border(true);
            $this->outline = new Border(true);
            $this->inside = new Border(true);
            $this->vertical = new Border(true);
            $this->horizontal = new Border(true);

            
            $this->left->bindParent($this, 'left');
            $this->right->bindParent($this, 'right');
            $this->top->bindParent($this, 'top');
            $this->bottom->bindParent($this, 'bottom');
            $this->diagonal->bindParent($this, 'diagonal');
            $this->allBorders->bindParent($this, 'allBorders');
            $this->outline->bindParent($this, 'outline');
            $this->inside->bindParent($this, 'inside');
            $this->vertical->bindParent($this, 'vertical');
            $this->horizontal->bindParent($this, 'horizontal');
        }
    }

    
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getBorders();
    }

    
    public function getStyleArray($array)
    {
        return ['borders' => $array];
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['left'])) {
                $this->getLeft()->applyFromArray($pStyles['left']);
            }
            if (isset($pStyles['right'])) {
                $this->getRight()->applyFromArray($pStyles['right']);
            }
            if (isset($pStyles['top'])) {
                $this->getTop()->applyFromArray($pStyles['top']);
            }
            if (isset($pStyles['bottom'])) {
                $this->getBottom()->applyFromArray($pStyles['bottom']);
            }
            if (isset($pStyles['diagonal'])) {
                $this->getDiagonal()->applyFromArray($pStyles['diagonal']);
            }
            if (isset($pStyles['diagonalDirection'])) {
                $this->setDiagonalDirection($pStyles['diagonalDirection']);
            }
            if (isset($pStyles['allBorders'])) {
                $this->getLeft()->applyFromArray($pStyles['allBorders']);
                $this->getRight()->applyFromArray($pStyles['allBorders']);
                $this->getTop()->applyFromArray($pStyles['allBorders']);
                $this->getBottom()->applyFromArray($pStyles['allBorders']);
            }
        }

        return $this;
    }

    
    public function getLeft()
    {
        return $this->left;
    }

    
    public function getRight()
    {
        return $this->right;
    }

    
    public function getTop()
    {
        return $this->top;
    }

    
    public function getBottom()
    {
        return $this->bottom;
    }

    
    public function getDiagonal()
    {
        return $this->diagonal;
    }

    
    public function getAllBorders()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->allBorders;
    }

    
    public function getOutline()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->outline;
    }

    
    public function getInside()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->inside;
    }

    
    public function getVertical()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->vertical;
    }

    
    public function getHorizontal()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->horizontal;
    }

    
    public function getDiagonalDirection()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getDiagonalDirection();
        }

        return $this->diagonalDirection;
    }

    
    public function setDiagonalDirection($pValue)
    {
        if ($pValue == '') {
            $pValue = self::DIAGONAL_NONE;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['diagonalDirection' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->diagonalDirection = $pValue;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashcode();
        }

        return md5(
            $this->getLeft()->getHashCode() .
            $this->getRight()->getHashCode() .
            $this->getTop()->getHashCode() .
            $this->getBottom()->getHashCode() .
            $this->getDiagonal()->getHashCode() .
            $this->getDiagonalDirection() .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'bottom', $this->getBottom());
        $this->exportArray2($exportedArray, 'diagonal', $this->getDiagonal());
        $this->exportArray2($exportedArray, 'diagonalDirection', $this->getDiagonalDirection());
        $this->exportArray2($exportedArray, 'left', $this->getLeft());
        $this->exportArray2($exportedArray, 'right', $this->getRight());
        $this->exportArray2($exportedArray, 'top', $this->getTop());

        return $exportedArray;
    }
}
