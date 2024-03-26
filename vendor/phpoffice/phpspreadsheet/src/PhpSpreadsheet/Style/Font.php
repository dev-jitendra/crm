<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Font extends Supervisor
{
    
    const UNDERLINE_NONE = 'none';
    const UNDERLINE_DOUBLE = 'double';
    const UNDERLINE_DOUBLEACCOUNTING = 'doubleAccounting';
    const UNDERLINE_SINGLE = 'single';
    const UNDERLINE_SINGLEACCOUNTING = 'singleAccounting';

    
    protected $name = 'Calibri';

    
    protected $size = 11;

    
    protected $bold = false;

    
    protected $italic = false;

    
    protected $superscript = false;

    
    protected $subscript = false;

    
    protected $underline = self::UNDERLINE_NONE;

    
    protected $strikethrough = false;

    
    protected $color;

    
    public $colorIndex;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        if ($isConditional) {
            $this->name = null;
            $this->size = null;
            $this->bold = null;
            $this->italic = null;
            $this->superscript = null;
            $this->subscript = null;
            $this->underline = null;
            $this->strikethrough = null;
            $this->color = new Color(Color::COLOR_BLACK, $isSupervisor, $isConditional);
        } else {
            $this->color = new Color(Color::COLOR_BLACK, $isSupervisor);
        }
        
        if ($isSupervisor) {
            $this->color->bindParent($this, 'color');
        }
    }

    
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getFont();
    }

    
    public function getStyleArray($array)
    {
        return ['font' => $array];
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['name'])) {
                $this->setName($pStyles['name']);
            }
            if (isset($pStyles['bold'])) {
                $this->setBold($pStyles['bold']);
            }
            if (isset($pStyles['italic'])) {
                $this->setItalic($pStyles['italic']);
            }
            if (isset($pStyles['superscript'])) {
                $this->setSuperscript($pStyles['superscript']);
            }
            if (isset($pStyles['subscript'])) {
                $this->setSubscript($pStyles['subscript']);
            }
            if (isset($pStyles['underline'])) {
                $this->setUnderline($pStyles['underline']);
            }
            if (isset($pStyles['strikethrough'])) {
                $this->setStrikethrough($pStyles['strikethrough']);
            }
            if (isset($pStyles['color'])) {
                $this->getColor()->applyFromArray($pStyles['color']);
            }
            if (isset($pStyles['size'])) {
                $this->setSize($pStyles['size']);
            }
        }

        return $this;
    }

    
    public function getName()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getName();
        }

        return $this->name;
    }

    
    public function setName($pValue)
    {
        if ($pValue == '') {
            $pValue = 'Calibri';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['name' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->name = $pValue;
        }

        return $this;
    }

    
    public function getSize()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSize();
        }

        return $this->size;
    }

    
    public function setSize($pValue)
    {
        if ($pValue == '') {
            $pValue = 10;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['size' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->size = $pValue;
        }

        return $this;
    }

    
    public function getBold()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBold();
        }

        return $this->bold;
    }

    
    public function setBold($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['bold' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->bold = $pValue;
        }

        return $this;
    }

    
    public function getItalic()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getItalic();
        }

        return $this->italic;
    }

    
    public function setItalic($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['italic' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->italic = $pValue;
        }

        return $this;
    }

    
    public function getSuperscript()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSuperscript();
        }

        return $this->superscript;
    }

    
    public function setSuperscript(bool $pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['superscript' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->superscript = $pValue;
            if ($this->superscript) {
                $this->subscript = false;
            }
        }

        return $this;
    }

    
    public function getSubscript()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSubscript();
        }

        return $this->subscript;
    }

    
    public function setSubscript(bool $pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['subscript' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->subscript = $pValue;
            if ($this->subscript) {
                $this->superscript = false;
            }
        }

        return $this;
    }

    
    public function getUnderline()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getUnderline();
        }

        return $this->underline;
    }

    
    public function setUnderline($pValue)
    {
        if (is_bool($pValue)) {
            $pValue = ($pValue) ? self::UNDERLINE_SINGLE : self::UNDERLINE_NONE;
        } elseif ($pValue == '') {
            $pValue = self::UNDERLINE_NONE;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['underline' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->underline = $pValue;
        }

        return $this;
    }

    
    public function getStrikethrough()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getStrikethrough();
        }

        return $this->strikethrough;
    }

    
    public function setStrikethrough($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['strikethrough' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->strikethrough = $pValue;
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
            $this->name .
            $this->size .
            ($this->bold ? 't' : 'f') .
            ($this->italic ? 't' : 'f') .
            ($this->superscript ? 't' : 'f') .
            ($this->subscript ? 't' : 'f') .
            $this->underline .
            ($this->strikethrough ? 't' : 'f') .
            $this->color->getHashCode() .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'bold', $this->getBold());
        $this->exportArray2($exportedArray, 'color', $this->getColor());
        $this->exportArray2($exportedArray, 'italic', $this->getItalic());
        $this->exportArray2($exportedArray, 'name', $this->getName());
        $this->exportArray2($exportedArray, 'size', $this->getSize());
        $this->exportArray2($exportedArray, 'strikethrough', $this->getStrikethrough());
        $this->exportArray2($exportedArray, 'subscript', $this->getSubscript());
        $this->exportArray2($exportedArray, 'superscript', $this->getSuperscript());
        $this->exportArray2($exportedArray, 'underline', $this->getUnderline());

        return $exportedArray;
    }
}
