<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseWriter
{
    
    private $spreadsheet;

    
    private $delimiter = ',';

    
    private $enclosure = '"';

    
    private $lineEnding = PHP_EOL;

    
    private $sheetIndex = 0;

    
    private $useBOM = false;

    
    private $includeSeparatorLine = false;

    
    private $excelCompatibility = false;

    
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    
    public function save($pFilename): void
    {
        
        $sheet = $this->spreadsheet->getSheet($this->sheetIndex);

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = Calculation::getArrayReturnType();
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);

        
        $this->openFileHandle($pFilename);

        if ($this->excelCompatibility) {
            $this->setUseBOM(true); 
            $this->setIncludeSeparatorLine(true); 
            $this->setEnclosure('"'); 
            $this->setDelimiter(';'); 
            $this->setLineEnding("\r\n");
        }

        if ($this->useBOM) {
            
            fwrite($this->fileHandle, "\xEF\xBB\xBF");
        }

        if ($this->includeSeparatorLine) {
            
            fwrite($this->fileHandle, 'sep=' . $this->getDelimiter() . $this->lineEnding);
        }

        
        $maxCol = $sheet->getHighestDataColumn();
        $maxRow = $sheet->getHighestDataRow();

        
        for ($row = 1; $row <= $maxRow; ++$row) {
            
            $cellsArray = $sheet->rangeToArray('A' . $row . ':' . $maxCol . $row, '', $this->preCalculateFormulas);
            
            $this->writeLine($this->fileHandle, $cellsArray[0]);
        }

        $this->maybeCloseFileHandle();
        Calculation::setArrayReturnType($saveArrayReturnType);
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);
    }

    
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    
    public function setDelimiter($pValue)
    {
        $this->delimiter = $pValue;

        return $this;
    }

    
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    
    public function setEnclosure($pValue = '"')
    {
        $this->enclosure = $pValue;

        return $this;
    }

    
    public function getLineEnding()
    {
        return $this->lineEnding;
    }

    
    public function setLineEnding($pValue)
    {
        $this->lineEnding = $pValue;

        return $this;
    }

    
    public function getUseBOM()
    {
        return $this->useBOM;
    }

    
    public function setUseBOM($pValue)
    {
        $this->useBOM = $pValue;

        return $this;
    }

    
    public function getIncludeSeparatorLine()
    {
        return $this->includeSeparatorLine;
    }

    
    public function setIncludeSeparatorLine($pValue)
    {
        $this->includeSeparatorLine = $pValue;

        return $this;
    }

    
    public function getExcelCompatibility()
    {
        return $this->excelCompatibility;
    }

    
    public function setExcelCompatibility($pValue)
    {
        $this->excelCompatibility = $pValue;

        return $this;
    }

    
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    private $enclosureRequired = true;

    public function setEnclosureRequired(bool $value): self
    {
        $this->enclosureRequired = $value;

        return $this;
    }

    public function getEnclosureRequired(): bool
    {
        return $this->enclosureRequired;
    }

    
    private function writeLine($pFileHandle, array $pValues): void
    {
        
        $delimiter = '';

        
        $line = '';

        foreach ($pValues as $element) {
            
            $line .= $delimiter;
            $delimiter = $this->delimiter;
            
            $enclosure = $this->enclosure;
            if ($enclosure) {
                
                
                if (!$this->enclosureRequired && strpbrk($element, "$delimiter$enclosure\n") === false) {
                    $enclosure = '';
                } else {
                    $element = str_replace($enclosure, $enclosure . $enclosure, $element);
                }
            }
            
            $line .= $enclosure . $element . $enclosure;
        }

        
        $line .= $this->lineEnding;

        
        fwrite($pFileHandle, $line);
    }
}
