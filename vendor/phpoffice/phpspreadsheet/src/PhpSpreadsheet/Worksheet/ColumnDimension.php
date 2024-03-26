<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class ColumnDimension extends Dimension
{
    
    private $columnIndex;

    
    private $width = -1;

    
    private $autoSize = false;

    
    public function __construct($pIndex = 'A')
    {
        
        $this->columnIndex = $pIndex;

        
        parent::__construct(0);
    }

    
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    
    public function setColumnIndex($pValue)
    {
        $this->columnIndex = $pValue;

        return $this;
    }

    
    public function getWidth()
    {
        return $this->width;
    }

    
    public function setWidth($pValue)
    {
        $this->width = $pValue;

        return $this;
    }

    
    public function getAutoSize()
    {
        return $this->autoSize;
    }

    
    public function setAutoSize($pValue)
    {
        $this->autoSize = $pValue;

        return $this;
    }
}
