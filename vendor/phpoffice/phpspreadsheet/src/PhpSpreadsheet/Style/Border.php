<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Border extends Supervisor
{
    
    const BORDER_NONE = 'none';
    const BORDER_DASHDOT = 'dashDot';
    const BORDER_DASHDOTDOT = 'dashDotDot';
    const BORDER_DASHED = 'dashed';
    const BORDER_DOTTED = 'dotted';
    const BORDER_DOUBLE = 'double';
    const BORDER_HAIR = 'hair';
    const BORDER_MEDIUM = 'medium';
    const BORDER_MEDIUMDASHDOT = 'mediumDashDot';
    const BORDER_MEDIUMDASHDOTDOT = 'mediumDashDotDot';
    const BORDER_MEDIUMDASHED = 'mediumDashed';
    const BORDER_SLANTDASHDOT = 'slantDashDot';
    const BORDER_THICK = 'thick';
    const BORDER_THIN = 'thin';

    
    protected $borderStyle = self::BORDER_NONE;

    
    protected $color;

    
    public $colorIndex;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        $this->color = new Color(Color::COLOR_BLACK, $isSupervisor);

        
        if ($isSupervisor) {
            $this->color->bindParent($this, 'color');
        }
    }

    
    public function getSharedComponent()
    {
        switch ($this->parentPropertyName) {
            case 'bottom':
                return $this->parent->getSharedComponent()->getBottom();
            case 'diagonal':
                return $this->parent->getSharedComponent()->getDiagonal();
            case 'left':
                return $this->parent->getSharedComponent()->getLeft();
            case 'right':
                return $this->parent->getSharedComponent()->getRight();
            case 'top':
                return $this->parent->getSharedComponent()->getTop();
        }

        throw new PhpSpreadsheetException('Cannot get shared component for a pseudo-border.');
    }

    
    public function getStyleArray($array)
    {
        return $this->parent->getStyleArray([$this->parentPropertyName => $array]);
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['borderStyle'])) {
                $this->setBorderStyle($pStyles['borderStyle']);
            }
            if (isset($pStyles['color'])) {
                $this->getColor()->applyFromArray($pStyles['color']);
            }
        }

        return $this;
    }

    
    public function getBorderStyle()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBorderStyle();
        }

        return $this->borderStyle;
    }

    
    public function setBorderStyle($pValue)
    {
        if (empty($pValue)) {
            $pValue = self::BORDER_NONE;
        } elseif (is_bool($pValue) && $pValue) {
            $pValue = self::BORDER_MEDIUM;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['borderStyle' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->borderStyle = $pValue;
        }

        return $this;
    }

    
    public function getColor()
    {
        return $this->color;
    }

    
    public function setColor(Color $pValue)
    {
        
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->color = $color;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->borderStyle .
            $this->color->getHashCode() .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'borderStyle', $this->getBorderStyle());
        $this->exportArray2($exportedArray, 'color', $this->getColor());

        return $exportedArray;
    }
}
