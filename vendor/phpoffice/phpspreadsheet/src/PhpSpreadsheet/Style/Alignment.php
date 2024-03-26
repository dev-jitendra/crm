<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Alignment extends Supervisor
{
    
    const HORIZONTAL_GENERAL = 'general';
    const HORIZONTAL_LEFT = 'left';
    const HORIZONTAL_RIGHT = 'right';
    const HORIZONTAL_CENTER = 'center';
    const HORIZONTAL_CENTER_CONTINUOUS = 'centerContinuous';
    const HORIZONTAL_JUSTIFY = 'justify';
    const HORIZONTAL_FILL = 'fill';
    const HORIZONTAL_DISTRIBUTED = 'distributed'; 

    
    const VERTICAL_BOTTOM = 'bottom';
    const VERTICAL_TOP = 'top';
    const VERTICAL_CENTER = 'center';
    const VERTICAL_JUSTIFY = 'justify';
    const VERTICAL_DISTRIBUTED = 'distributed'; 

    
    const READORDER_CONTEXT = 0;
    const READORDER_LTR = 1;
    const READORDER_RTL = 2;

    
    const TEXTROTATION_STACK_EXCEL = 255;
    const TEXTROTATION_STACK_PHPSPREADSHEET = -165; 

    
    protected $horizontal = self::HORIZONTAL_GENERAL;

    
    protected $vertical = self::VERTICAL_BOTTOM;

    
    protected $textRotation = 0;

    
    protected $wrapText = false;

    
    protected $shrinkToFit = false;

    
    protected $indent = 0;

    
    protected $readOrder = 0;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        if ($isConditional) {
            $this->horizontal = null;
            $this->vertical = null;
            $this->textRotation = null;
        }
    }

    
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getAlignment();
    }

    
    public function getStyleArray($array)
    {
        return ['alignment' => $array];
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())
                ->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['horizontal'])) {
                $this->setHorizontal($pStyles['horizontal']);
            }
            if (isset($pStyles['vertical'])) {
                $this->setVertical($pStyles['vertical']);
            }
            if (isset($pStyles['textRotation'])) {
                $this->setTextRotation($pStyles['textRotation']);
            }
            if (isset($pStyles['wrapText'])) {
                $this->setWrapText($pStyles['wrapText']);
            }
            if (isset($pStyles['shrinkToFit'])) {
                $this->setShrinkToFit($pStyles['shrinkToFit']);
            }
            if (isset($pStyles['indent'])) {
                $this->setIndent($pStyles['indent']);
            }
            if (isset($pStyles['readOrder'])) {
                $this->setReadOrder($pStyles['readOrder']);
            }
        }

        return $this;
    }

    
    public function getHorizontal()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHorizontal();
        }

        return $this->horizontal;
    }

    
    public function setHorizontal($pValue)
    {
        if ($pValue == '') {
            $pValue = self::HORIZONTAL_GENERAL;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['horizontal' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->horizontal = $pValue;
        }

        return $this;
    }

    
    public function getVertical()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getVertical();
        }

        return $this->vertical;
    }

    
    public function setVertical($pValue)
    {
        if ($pValue == '') {
            $pValue = self::VERTICAL_BOTTOM;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['vertical' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->vertical = $pValue;
        }

        return $this;
    }

    
    public function getTextRotation()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getTextRotation();
        }

        return $this->textRotation;
    }

    
    public function setTextRotation($pValue)
    {
        
        if ($pValue == self::TEXTROTATION_STACK_EXCEL) {
            $pValue = self::TEXTROTATION_STACK_PHPSPREADSHEET;
        }

        
        if (($pValue >= -90 && $pValue <= 90) || $pValue == self::TEXTROTATION_STACK_PHPSPREADSHEET) {
            if ($this->isSupervisor) {
                $styleArray = $this->getStyleArray(['textRotation' => $pValue]);
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            } else {
                $this->textRotation = $pValue;
            }
        } else {
            throw new PhpSpreadsheetException('Text rotation should be a value between -90 and 90.');
        }

        return $this;
    }

    
    public function getWrapText()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getWrapText();
        }

        return $this->wrapText;
    }

    
    public function setWrapText($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['wrapText' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->wrapText = $pValue;
        }

        return $this;
    }

    
    public function getShrinkToFit()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getShrinkToFit();
        }

        return $this->shrinkToFit;
    }

    
    public function setShrinkToFit($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['shrinkToFit' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->shrinkToFit = $pValue;
        }

        return $this;
    }

    
    public function getIndent()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getIndent();
        }

        return $this->indent;
    }

    
    public function setIndent($pValue)
    {
        if ($pValue > 0) {
            if (
                $this->getHorizontal() != self::HORIZONTAL_GENERAL &&
                $this->getHorizontal() != self::HORIZONTAL_LEFT &&
                $this->getHorizontal() != self::HORIZONTAL_RIGHT
            ) {
                $pValue = 0; 
            }
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['indent' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->indent = $pValue;
        }

        return $this;
    }

    
    public function getReadOrder()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getReadOrder();
        }

        return $this->readOrder;
    }

    
    public function setReadOrder($pValue)
    {
        if ($pValue < 0 || $pValue > 2) {
            $pValue = 0;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['readOrder' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->readOrder = $pValue;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->horizontal .
            $this->vertical .
            $this->textRotation .
            ($this->wrapText ? 't' : 'f') .
            ($this->shrinkToFit ? 't' : 'f') .
            $this->indent .
            $this->readOrder .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'horizontal', $this->getHorizontal());
        $this->exportArray2($exportedArray, 'indent', $this->getIndent());
        $this->exportArray2($exportedArray, 'readOrder', $this->getReadOrder());
        $this->exportArray2($exportedArray, 'shrinkToFit', $this->getShrinkToFit());
        $this->exportArray2($exportedArray, 'textRotation', $this->getTextRotation());
        $this->exportArray2($exportedArray, 'vertical', $this->getVertical());
        $this->exportArray2($exportedArray, 'wrapText', $this->getWrapText());

        return $exportedArray;
    }
}
