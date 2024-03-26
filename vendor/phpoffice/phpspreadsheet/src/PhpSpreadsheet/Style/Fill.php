<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Fill extends Supervisor
{
    
    const FILL_NONE = 'none';
    const FILL_SOLID = 'solid';
    const FILL_GRADIENT_LINEAR = 'linear';
    const FILL_GRADIENT_PATH = 'path';
    const FILL_PATTERN_DARKDOWN = 'darkDown';
    const FILL_PATTERN_DARKGRAY = 'darkGray';
    const FILL_PATTERN_DARKGRID = 'darkGrid';
    const FILL_PATTERN_DARKHORIZONTAL = 'darkHorizontal';
    const FILL_PATTERN_DARKTRELLIS = 'darkTrellis';
    const FILL_PATTERN_DARKUP = 'darkUp';
    const FILL_PATTERN_DARKVERTICAL = 'darkVertical';
    const FILL_PATTERN_GRAY0625 = 'gray0625';
    const FILL_PATTERN_GRAY125 = 'gray125';
    const FILL_PATTERN_LIGHTDOWN = 'lightDown';
    const FILL_PATTERN_LIGHTGRAY = 'lightGray';
    const FILL_PATTERN_LIGHTGRID = 'lightGrid';
    const FILL_PATTERN_LIGHTHORIZONTAL = 'lightHorizontal';
    const FILL_PATTERN_LIGHTTRELLIS = 'lightTrellis';
    const FILL_PATTERN_LIGHTUP = 'lightUp';
    const FILL_PATTERN_LIGHTVERTICAL = 'lightVertical';
    const FILL_PATTERN_MEDIUMGRAY = 'mediumGray';

    
    public $startcolorIndex;

    
    public $endcolorIndex;

    
    protected $fillType = self::FILL_NONE;

    
    protected $rotation = 0;

    
    protected $startColor;

    
    protected $endColor;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        
        parent::__construct($isSupervisor);

        
        if ($isConditional) {
            $this->fillType = null;
        }
        $this->startColor = new Color(Color::COLOR_WHITE, $isSupervisor, $isConditional);
        $this->endColor = new Color(Color::COLOR_BLACK, $isSupervisor, $isConditional);

        
        if ($isSupervisor) {
            $this->startColor->bindParent($this, 'startColor');
            $this->endColor->bindParent($this, 'endColor');
        }
    }

    
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getFill();
    }

    
    public function getStyleArray($array)
    {
        return ['fill' => $array];
    }

    
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['fillType'])) {
                $this->setFillType($pStyles['fillType']);
            }
            if (isset($pStyles['rotation'])) {
                $this->setRotation($pStyles['rotation']);
            }
            if (isset($pStyles['startColor'])) {
                $this->getStartColor()->applyFromArray($pStyles['startColor']);
            }
            if (isset($pStyles['endColor'])) {
                $this->getEndColor()->applyFromArray($pStyles['endColor']);
            }
            if (isset($pStyles['color'])) {
                $this->getStartColor()->applyFromArray($pStyles['color']);
                $this->getEndColor()->applyFromArray($pStyles['color']);
            }
        }

        return $this;
    }

    
    public function getFillType()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getFillType();
        }

        return $this->fillType;
    }

    
    public function setFillType($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['fillType' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->fillType = $pValue;
        }

        return $this;
    }

    
    public function getRotation()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRotation();
        }

        return $this->rotation;
    }

    
    public function setRotation($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['rotation' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->rotation = $pValue;
        }

        return $this;
    }

    
    public function getStartColor()
    {
        return $this->startColor;
    }

    
    public function setStartColor(Color $pValue)
    {
        
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getStartColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->startColor = $color;
        }

        return $this;
    }

    
    public function getEndColor()
    {
        return $this->endColor;
    }

    
    public function setEndColor(Color $pValue)
    {
        
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getEndColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->endColor = $color;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }
        
        
        return md5(
            $this->getFillType() .
            $this->getRotation() .
            ($this->getFillType() !== self::FILL_NONE ? $this->getStartColor()->getHashCode() : '') .
            ($this->getFillType() !== self::FILL_NONE ? $this->getEndColor()->getHashCode() : '') .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'endColor', $this->getEndColor());
        $this->exportArray2($exportedArray, 'fillType', $this->getFillType());
        $this->exportArray2($exportedArray, 'rotation', $this->getRotation());
        $this->exportArray2($exportedArray, 'startColor', $this->getStartColor());

        return $exportedArray;
    }
}
