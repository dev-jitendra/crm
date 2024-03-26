<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Drawing extends BaseDrawing
{
    
    private $path;

    
    public function __construct()
    {
        
        $this->path = '';

        
        parent::__construct();
    }

    
    public function getFilename()
    {
        return basename($this->path);
    }

    
    public function getIndexedFilename()
    {
        $fileName = $this->getFilename();
        $fileName = str_replace(' ', '_', $fileName);

        return str_replace('.' . $this->getExtension(), '', $fileName) . $this->getImageIndex() . '.' . $this->getExtension();
    }

    
    public function getExtension()
    {
        $exploded = explode('.', basename($this->path));

        return $exploded[count($exploded) - 1];
    }

    
    public function getPath()
    {
        return $this->path;
    }

    
    public function setPath($pValue, $pVerifyFile = true)
    {
        if ($pVerifyFile) {
            if (file_exists($pValue)) {
                $this->path = $pValue;

                if ($this->width == 0 && $this->height == 0) {
                    
                    [$this->width, $this->height] = getimagesize($pValue);
                }
            } else {
                throw new PhpSpreadsheetException("File $pValue not found!");
            }
        } else {
            $this->path = $pValue;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->path .
            parent::getHashCode() .
            __CLASS__
        );
    }
}
