<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Theme
{
    
    private $themeName;

    
    private $colourSchemeName;

    
    private $colourMap;

    
    public function __construct($themeName, $colourSchemeName, $colourMap)
    {
        
        $this->themeName = $themeName;
        $this->colourSchemeName = $colourSchemeName;
        $this->colourMap = $colourMap;
    }

    
    public function getThemeName()
    {
        return $this->themeName;
    }

    
    public function getColourSchemeName()
    {
        return $this->colourSchemeName;
    }

    
    public function getColourByIndex($index)
    {
        if (isset($this->colourMap[$index])) {
            return $this->colourMap[$index];
        }

        return null;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != '_parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
