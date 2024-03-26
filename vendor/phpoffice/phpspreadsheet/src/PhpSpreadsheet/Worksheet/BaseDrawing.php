<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IComparable;

class BaseDrawing implements IComparable
{
    
    private static $imageCounter = 0;

    
    private $imageIndex = 0;

    
    protected $name;

    
    protected $description;

    
    protected $worksheet;

    
    protected $coordinates;

    
    protected $offsetX;

    
    protected $offsetY;

    
    protected $width;

    
    protected $height;

    
    protected $resizeProportional;

    
    protected $rotation;

    
    protected $shadow;

    
    private $hyperlink;

    
    public function __construct()
    {
        
        $this->name = '';
        $this->description = '';
        $this->worksheet = null;
        $this->coordinates = 'A1';
        $this->offsetX = 0;
        $this->offsetY = 0;
        $this->width = 0;
        $this->height = 0;
        $this->resizeProportional = true;
        $this->rotation = 0;
        $this->shadow = new Drawing\Shadow();

        
        ++self::$imageCounter;
        $this->imageIndex = self::$imageCounter;
    }

    
    public function getImageIndex()
    {
        return $this->imageIndex;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function setName($pValue)
    {
        $this->name = $pValue;

        return $this;
    }

    
    public function getDescription()
    {
        return $this->description;
    }

    
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    
    public function setWorksheet(?Worksheet $pValue = null, $pOverrideOld = false)
    {
        if ($this->worksheet === null) {
            
            $this->worksheet = $pValue;
            $this->worksheet->getCell($this->coordinates);
            $this->worksheet->getDrawingCollection()->append($this);
        } else {
            if ($pOverrideOld) {
                
                $iterator = $this->worksheet->getDrawingCollection()->getIterator();

                while ($iterator->valid()) {
                    if ($iterator->current()->getHashCode() === $this->getHashCode()) {
                        $this->worksheet->getDrawingCollection()->offsetUnset($iterator->key());
                        $this->worksheet = null;

                        break;
                    }
                }

                
                $this->setWorksheet($pValue);
            } else {
                throw new PhpSpreadsheetException('A Worksheet has already been assigned. Drawings can only exist on one \\PhpOffice\\PhpSpreadsheet\\Worksheet.');
            }
        }

        return $this;
    }

    
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    
    public function setCoordinates($pValue)
    {
        $this->coordinates = $pValue;

        return $this;
    }

    
    public function getOffsetX()
    {
        return $this->offsetX;
    }

    
    public function setOffsetX($pValue)
    {
        $this->offsetX = $pValue;

        return $this;
    }

    
    public function getOffsetY()
    {
        return $this->offsetY;
    }

    
    public function setOffsetY($pValue)
    {
        $this->offsetY = $pValue;

        return $this;
    }

    
    public function getWidth()
    {
        return $this->width;
    }

    
    public function setWidth($pValue)
    {
        
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->height / ($this->width != 0 ? $this->width : 1);
            $this->height = round($ratio * $pValue);
        }

        
        $this->width = $pValue;

        return $this;
    }

    
    public function getHeight()
    {
        return $this->height;
    }

    
    public function setHeight($pValue)
    {
        
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->width / ($this->height != 0 ? $this->height : 1);
            $this->width = round($ratio * $pValue);
        }

        
        $this->height = $pValue;

        return $this;
    }

    
    public function setWidthAndHeight($width, $height)
    {
        $xratio = $width / ($this->width != 0 ? $this->width : 1);
        $yratio = $height / ($this->height != 0 ? $this->height : 1);
        if ($this->resizeProportional && !($width == 0 || $height == 0)) {
            if (($xratio * $this->height) < $height) {
                $this->height = ceil($xratio * $this->height);
                $this->width = $width;
            } else {
                $this->width = ceil($yratio * $this->width);
                $this->height = $height;
            }
        } else {
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    }

    
    public function getResizeProportional()
    {
        return $this->resizeProportional;
    }

    
    public function setResizeProportional($pValue)
    {
        $this->resizeProportional = $pValue;

        return $this;
    }

    
    public function getRotation()
    {
        return $this->rotation;
    }

    
    public function setRotation($pValue)
    {
        $this->rotation = $pValue;

        return $this;
    }

    
    public function getShadow()
    {
        return $this->shadow;
    }

    
    public function setShadow(?Drawing\Shadow $pValue = null)
    {
        $this->shadow = $pValue;

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->name .
            $this->description .
            $this->worksheet->getHashCode() .
            $this->coordinates .
            $this->offsetX .
            $this->offsetY .
            $this->width .
            $this->height .
            $this->rotation .
            $this->shadow->getHashCode() .
            __CLASS__
        );
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ($key == 'worksheet') {
                $this->worksheet = null;
            } elseif (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    public function setHyperlink(?Hyperlink $pHyperlink = null): void
    {
        $this->hyperlink = $pHyperlink;
    }

    
    public function getHyperlink()
    {
        return $this->hyperlink;
    }
}
