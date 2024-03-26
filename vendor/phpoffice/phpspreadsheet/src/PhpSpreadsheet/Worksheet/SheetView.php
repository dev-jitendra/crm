<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class SheetView
{
    
    const SHEETVIEW_NORMAL = 'normal';
    const SHEETVIEW_PAGE_LAYOUT = 'pageLayout';
    const SHEETVIEW_PAGE_BREAK_PREVIEW = 'pageBreakPreview';

    private static $sheetViewTypes = [
        self::SHEETVIEW_NORMAL,
        self::SHEETVIEW_PAGE_LAYOUT,
        self::SHEETVIEW_PAGE_BREAK_PREVIEW,
    ];

    
    private $zoomScale = 100;

    
    private $zoomScaleNormal = 100;

    
    private $showZeros = true;

    
    private $sheetviewType = self::SHEETVIEW_NORMAL;

    
    public function __construct()
    {
    }

    
    public function getZoomScale()
    {
        return $this->zoomScale;
    }

    
    public function setZoomScale($pValue)
    {
        
        
        if (($pValue >= 1) || $pValue === null) {
            $this->zoomScale = $pValue;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    
    public function getZoomScaleNormal()
    {
        return $this->zoomScaleNormal;
    }

    
    public function setZoomScaleNormal($pValue)
    {
        if (($pValue >= 1) || $pValue === null) {
            $this->zoomScaleNormal = $pValue;
        } else {
            throw new PhpSpreadsheetException('Scale must be greater than or equal to 1.');
        }

        return $this;
    }

    
    public function setShowZeros($pValue): void
    {
        $this->showZeros = $pValue;
    }

    
    public function getShowZeros()
    {
        return $this->showZeros;
    }

    
    public function getView()
    {
        return $this->sheetviewType;
    }

    
    public function setView($pValue)
    {
        
        if ($pValue === null) {
            $pValue = self::SHEETVIEW_NORMAL;
        }
        if (in_array($pValue, self::$sheetViewTypes)) {
            $this->sheetviewType = $pValue;
        } else {
            throw new PhpSpreadsheetException('Invalid sheetview layout type.');
        }

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
