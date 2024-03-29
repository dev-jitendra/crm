<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

abstract class Dimension
{
    
    private $visible = true;

    
    private $outlineLevel = 0;

    
    private $collapsed = false;

    
    private $xfIndex;

    
    public function __construct($initialValue = null)
    {
        
        $this->xfIndex = $initialValue;
    }

    
    public function getVisible()
    {
        return $this->visible;
    }

    
    public function setVisible($pValue)
    {
        $this->visible = (bool) $pValue;

        return $this;
    }

    
    public function getOutlineLevel()
    {
        return $this->outlineLevel;
    }

    
    public function setOutlineLevel($pValue)
    {
        if ($pValue < 0 || $pValue > 7) {
            throw new PhpSpreadsheetException('Outline level must range between 0 and 7.');
        }

        $this->outlineLevel = $pValue;

        return $this;
    }

    
    public function getCollapsed()
    {
        return $this->collapsed;
    }

    
    public function setCollapsed($pValue)
    {
        $this->collapsed = (bool) $pValue;

        return $this;
    }

    
    public function getXfIndex()
    {
        return $this->xfIndex;
    }

    
    public function setXfIndex($pValue)
    {
        $this->xfIndex = $pValue;

        return $this;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
