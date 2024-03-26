<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Slk extends BaseReader
{
    
    private $inputEncoding = 'ANSI';

    
    private $sheetIndex = 0;

    
    private $formats = [];

    
    private $format = 0;

    
    private $fonts = [];

    
    private $fontcount = 0;

    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function canRead($pFilename)
    {
        try {
            $this->openFile($pFilename);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        
        $data = fread($this->fileHandle, 2048);

        
        $delimiterCount = substr_count($data, ';');
        $hasDelimiter = $delimiterCount > 0;

        
        $lines = explode("\n", $data);
        $hasId = substr($lines[0], 0, 4) === 'ID;P';

        fclose($this->fileHandle);

        return $hasDelimiter && $hasId;
    }

    private function canReadOrBust(string $pFilename): void
    {
        if (!$this->canRead($pFilename)) {
            throw new ReaderException($pFilename . ' is an Invalid SYLK file.');
        }
        $this->openFile($pFilename);
    }

    
    public function setInputEncoding($pValue)
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    
    public function listWorksheetInfo($pFilename)
    {
        
        $this->canReadOrBust($pFilename);
        $fileHandle = $this->fileHandle;
        rewind($fileHandle);

        $worksheetInfo = [];
        $worksheetInfo[0]['worksheetName'] = basename($pFilename, '.slk');

        
        $rowIndex = 0;
        $columnIndex = 0;
        while (($rowData = fgets($fileHandle)) !== false) {
            $columnIndex = 0;

            
            $rowData = StringHelper::SYLKtoUTF8($rowData);

            
            
            $rowData = explode("\t", str_replace('¤', ';', str_replace(';', "\t", str_replace(';;', '¤', rtrim($rowData)))));

            $dataType = array_shift($rowData);
            if ($dataType == 'B') {
                foreach ($rowData as $rowDatum) {
                    switch ($rowDatum[0]) {
                        case 'X':
                            $columnIndex = substr($rowDatum, 1) - 1;

                            break;
                        case 'Y':
                            $rowIndex = substr($rowDatum, 1);

                            break;
                    }
                }

                break;
            }
        }

        $worksheetInfo[0]['lastColumnIndex'] = $columnIndex;
        $worksheetInfo[0]['totalRows'] = $rowIndex;
        $worksheetInfo[0]['lastColumnLetter'] = Coordinate::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex'] + 1);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        
        fclose($fileHandle);

        return $worksheetInfo;
    }

    
    public function load($pFilename)
    {
        
        $spreadsheet = new Spreadsheet();

        
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    private $colorArray = [
        'FF00FFFF', 
        'FF000000', 
        'FFFFFFFF', 
        'FFFF0000', 
        'FF00FF00', 
        'FF0000FF', 
        'FFFFFF00', 
        'FFFF00FF', 
    ];

    private $fontStyleMappings = [
        'B' => 'bold',
        'I' => 'italic',
        'U' => 'underline',
    ];

    private function processFormula(string $rowDatum, bool &$hasCalculatedValue, string &$cellDataFormula, string $row, string $column): void
    {
        $cellDataFormula = '=' . substr($rowDatum, 1);
        
        $temp = explode('"', $cellDataFormula);
        $key = false;
        foreach ($temp as &$value) {
            
            if ($key = !$key) {
                preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                
                
                
                $cellReferences = array_reverse($cellReferences);
                
                
                foreach ($cellReferences as $cellReference) {
                    $rowReference = $cellReference[2][0];
                    
                    if ($rowReference == '') {
                        $rowReference = $row;
                    }
                    
                    if ($rowReference[0] == '[') {
                        $rowReference = $row + trim($rowReference, '[]');
                    }
                    $columnReference = $cellReference[4][0];
                    
                    if ($columnReference == '') {
                        $columnReference = $column;
                    }
                    
                    if ($columnReference[0] == '[') {
                        $columnReference = $column + trim($columnReference, '[]');
                    }
                    $A1CellReference = Coordinate::stringFromColumnIndex($columnReference) . $rowReference;

                    $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                }
            }
        }
        unset($value);
        
        $cellDataFormula = implode('"', $temp);
        $hasCalculatedValue = true;
    }

    private function processCRecord(array $rowData, Spreadsheet &$spreadsheet, string &$row, string &$column): void
    {
        
        $hasCalculatedValue = false;
        $cellDataFormula = $cellData = '';
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'C':
                case 'X':
                    $column = substr($rowDatum, 1);

                    break;
                case 'R':
                case 'Y':
                    $row = substr($rowDatum, 1);

                    break;
                case 'K':
                    $cellData = substr($rowDatum, 1);

                    break;
                case 'E':
                    $this->processFormula($rowDatum, $hasCalculatedValue, $cellDataFormula, $row, $column);

                    break;
            }
        }
        $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
        $cellData = Calculation::unwrapResult($cellData);

        
        $this->processCFinal($spreadsheet, $hasCalculatedValue, $cellDataFormula, $cellData, "$columnLetter$row");
    }

    private function processCFinal(Spreadsheet &$spreadsheet, bool $hasCalculatedValue, string $cellDataFormula, string $cellData, string $coordinate): void
    {
        
        $spreadsheet->getActiveSheet()->getCell($coordinate)->setValue(($hasCalculatedValue) ? $cellDataFormula : $cellData);
        if ($hasCalculatedValue) {
            $cellData = Calculation::unwrapResult($cellData);
            $spreadsheet->getActiveSheet()->getCell($coordinate)->setCalculatedValue($cellData);
        }
    }

    private function processFRecord(array $rowData, Spreadsheet &$spreadsheet, string &$row, string &$column): void
    {
        
        $formatStyle = $columnWidth = '';
        $startCol = $endCol = '';
        $fontStyle = '';
        $styleData = [];
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'C':
                case 'X':
                    $column = substr($rowDatum, 1);

                    break;
                case 'R':
                case 'Y':
                    $row = substr($rowDatum, 1);

                    break;
                case 'P':
                    $formatStyle = $rowDatum;

                    break;
                case 'W':
                    [$startCol, $endCol, $columnWidth] = explode(' ', substr($rowDatum, 1));

                    break;
                case 'S':
                    $this->styleSettings($rowDatum, $styleData, $fontStyle);

                    break;
            }
        }
        $this->addFormats($spreadsheet, $formatStyle, $row, $column);
        $this->addFonts($spreadsheet, $fontStyle, $row, $column);
        $this->addStyle($spreadsheet, $styleData, $row, $column);
        $this->addWidth($spreadsheet, $columnWidth, $startCol, $endCol);
    }

    private $styleSettingsFont = ['D' => 'bold', 'I' => 'italic'];

    private $styleSettingsBorder = [
        'B' => 'bottom',
        'L' => 'left',
        'R' => 'right',
        'T' => 'top',
    ];

    private function styleSettings(string $rowDatum, array &$styleData, string &$fontStyle): void
    {
        $styleSettings = substr($rowDatum, 1);
        $iMax = strlen($styleSettings);
        for ($i = 0; $i < $iMax; ++$i) {
            $char = $styleSettings[$i];
            if (array_key_exists($char, $this->styleSettingsFont)) {
                $styleData['font'][$this->styleSettingsFont[$char]] = true;
            } elseif (array_key_exists($char, $this->styleSettingsBorder)) {
                $styleData['borders'][$this->styleSettingsBorder[$char]]['borderStyle'] = Border::BORDER_THIN;
            } elseif ($char == 'S') {
                $styleData['fill']['fillType'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_GRAY125;
            } elseif ($char == 'M') {
                if (preg_match('/M([1-9]\\d*)/', $styleSettings, $matches)) {
                    $fontStyle = $matches[1];
                }
            }
        }
    }

    private function addFormats(Spreadsheet &$spreadsheet, string $formatStyle, string $row, string $column): void
    {
        if ($formatStyle && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
            if (isset($this->formats[$formatStyle])) {
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($this->formats[$formatStyle]);
            }
        }
    }

    private function addFonts(Spreadsheet &$spreadsheet, string $fontStyle, string $row, string $column): void
    {
        if ($fontStyle && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
            if (isset($this->fonts[$fontStyle])) {
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($this->fonts[$fontStyle]);
            }
        }
    }

    private function addStyle(Spreadsheet &$spreadsheet, array $styleData, string $row, string $column): void
    {
        if ((!empty($styleData)) && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex($column);
            $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($styleData);
        }
    }

    private function addWidth(Spreadsheet $spreadsheet, string $columnWidth, string $startCol, string $endCol): void
    {
        if ($columnWidth > '') {
            if ($startCol == $endCol) {
                $startCol = Coordinate::stringFromColumnIndex((int) $startCol);
                $spreadsheet->getActiveSheet()->getColumnDimension($startCol)->setWidth($columnWidth);
            } else {
                $startCol = Coordinate::stringFromColumnIndex($startCol);
                $endCol = Coordinate::stringFromColumnIndex($endCol);
                $spreadsheet->getActiveSheet()->getColumnDimension($startCol)->setWidth((float) $columnWidth);
                do {
                    $spreadsheet->getActiveSheet()->getColumnDimension(++$startCol)->setWidth($columnWidth);
                } while ($startCol != $endCol);
            }
        }
    }

    private function processPRecord(array $rowData, Spreadsheet &$spreadsheet): void
    {
        
        $formatArray = [];
        $fromFormats = ['\-', '\ '];
        $toFormats = ['-', ' '];
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'P':
                    $formatArray['numberFormat']['formatCode'] = str_replace($fromFormats, $toFormats, substr($rowDatum, 1));

                    break;
                case 'E':
                case 'F':
                    $formatArray['font']['name'] = substr($rowDatum, 1);

                    break;
                case 'M':
                    $formatArray['font']['size'] = substr($rowDatum, 1) / 20;

                    break;
                case 'L':
                    $this->processPColors($rowDatum, $formatArray);

                    break;
                case 'S':
                    $this->processPFontStyles($rowDatum, $formatArray);

                    break;
            }
        }
        $this->processPFinal($spreadsheet, $formatArray);
    }

    private function processPColors(string $rowDatum, array &$formatArray): void
    {
        if (preg_match('/L([1-9]\\d*)/', $rowDatum, $matches)) {
            $fontColor = $matches[1] % 8;
            $formatArray['font']['color']['argb'] = $this->colorArray[$fontColor];
        }
    }

    private function processPFontStyles(string $rowDatum, array &$formatArray): void
    {
        $styleSettings = substr($rowDatum, 1);
        $iMax = strlen($styleSettings);
        for ($i = 0; $i < $iMax; ++$i) {
            if (array_key_exists($styleSettings[$i], $this->fontStyleMappings)) {
                $formatArray['font'][$this->fontStyleMappings[$styleSettings[$i]]] = true;
            }
        }
    }

    private function processPFinal(Spreadsheet &$spreadsheet, array $formatArray): void
    {
        if (array_key_exists('numberFormat', $formatArray)) {
            $this->formats['P' . $this->format] = $formatArray;
            ++$this->format;
        } elseif (array_key_exists('font', $formatArray)) {
            ++$this->fontcount;
            $this->fonts[$this->fontcount] = $formatArray;
            if ($this->fontcount === 1) {
                $spreadsheet->getDefaultStyle()->applyFromArray($formatArray);
            }
        }
    }

    
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        
        $this->canReadOrBust($pFilename);
        $fileHandle = $this->fileHandle;
        rewind($fileHandle);

        
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($this->sheetIndex);
        $spreadsheet->getActiveSheet()->setTitle(substr(basename($pFilename, '.slk'), 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH));

        
        $column = $row = '';

        
        while (($rowDataTxt = fgets($fileHandle)) !== false) {
            
            $rowDataTxt = StringHelper::SYLKtoUTF8($rowDataTxt);

            
            
            $rowData = explode("\t", str_replace('¤', ';', str_replace(';', "\t", str_replace(';;', '¤', rtrim($rowDataTxt)))));

            $dataType = array_shift($rowData);
            if ($dataType == 'P') {
                
                $this->processPRecord($rowData, $spreadsheet);
            } elseif ($dataType == 'C') {
                
                $this->processCRecord($rowData, $spreadsheet, $row, $column);
            } elseif ($dataType == 'F') {
                
                $this->processFRecord($rowData, $spreadsheet, $row, $column);
            } else {
                $this->columnRowFromRowData($rowData, $column, $row);
            }
        }

        
        fclose($fileHandle);

        
        return $spreadsheet;
    }

    private function columnRowFromRowData(array $rowData, string &$column, string &$row): void
    {
        foreach ($rowData as $rowDatum) {
            $char0 = $rowDatum[0];
            if ($char0 === 'X' || $char0 == 'C') {
                $column = substr($rowDatum, 1);
            } elseif ($char0 === 'Y' || $char0 == 'R') {
                $row = substr($rowDatum, 1);
            }
        }
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
}
