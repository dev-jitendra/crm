<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DefinedNames
{
    private $objWriter;

    private $spreadsheet;

    public function __construct(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        $this->objWriter = $objWriter;
        $this->spreadsheet = $spreadsheet;
    }

    public function write(): void
    {
        
        $this->objWriter->startElement('definedNames');

        
        if (count($this->spreadsheet->getDefinedNames()) > 0) {
            
            $this->writeNamedRangesAndFormulae();
        }

        
        $sheetCount = $this->spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            
            $this->writeNamedRangeForAutofilter($this->spreadsheet->getSheet($i), $i);

            
            $this->writeNamedRangeForPrintTitles($this->spreadsheet->getSheet($i), $i);

            
            $this->writeNamedRangeForPrintArea($this->spreadsheet->getSheet($i), $i);
        }

        $this->objWriter->endElement();
    }

    
    private function writeNamedRangesAndFormulae(): void
    {
        
        $definedNames = $this->spreadsheet->getDefinedNames();
        foreach ($definedNames as $definedName) {
            $this->writeDefinedName($definedName);
        }
    }

    
    private function writeDefinedName(DefinedName $pDefinedName): void
    {
        
        $this->objWriter->startElement('definedName');
        $this->objWriter->writeAttribute('name', $pDefinedName->getName());
        if ($pDefinedName->getLocalOnly() && $pDefinedName->getScope() !== null) {
            $this->objWriter->writeAttribute('localSheetId', $pDefinedName->getScope()->getParent()->getIndex($pDefinedName->getScope()));
        }

        $definedRange = $pDefinedName->getValue();
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
            $definedRange,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $lengths = array_map('strlen', array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);

        $worksheets = $splitRanges[2];
        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $worksheet = $worksheets[$splitCount][0];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            $newRange = '';
            if (empty($worksheet)) {
                if (($offset === 0) || ($definedRange[$offset - 1] !== ':')) {
                    
                    $worksheet = $pDefinedName->getWorksheet()->getTitle();
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "'" . str_replace("'", "''", $worksheet) . "'!";
            }

            if (!empty($column)) {
                $newRange .= $column;
            }
            if (!empty($row)) {
                $newRange .= $row;
            }

            $definedRange = substr($definedRange, 0, $offset) . $newRange . substr($definedRange, $offset + $length);
        }

        if (substr($definedRange, 0, 1) === '=') {
            $definedRange = substr($definedRange, 1);
        }

        $this->objWriter->writeRawData($definedRange);

        $this->objWriter->endElement();
    }

    
    private function writeNamedRangeForAutofilter(Worksheet $pSheet, int $pSheetId = 0): void
    {
        
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm._FilterDatabase');
            $this->objWriter->writeAttribute('localSheetId', $pSheetId);
            $this->objWriter->writeAttribute('hidden', '1');

            
            $range = Coordinate::splitRange($autoFilterRange);
            $range = $range[0];
            
            [$ws, $range[0]] = Worksheet::extractSheetTitle($range[0], true);

            $range[0] = Coordinate::absoluteCoordinate($range[0]);
            $range[1] = Coordinate::absoluteCoordinate($range[1]);
            $range = implode(':', $range);

            $this->objWriter->writeRawData('\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . $range);

            $this->objWriter->endElement();
        }
    }

    
    private function writeNamedRangeForPrintTitles(Worksheet $pSheet, int $pSheetId = 0): void
    {
        
        if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet() || $pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm.Print_Titles');
            $this->objWriter->writeAttribute('localSheetId', $pSheetId);

            
            $settingString = '';

            
            if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                $repeat = $pSheet->getPageSetup()->getColumnsToRepeatAtLeft();

                $settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            
            if ($pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
                if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                    $settingString .= ',';
                }

                $repeat = $pSheet->getPageSetup()->getRowsToRepeatAtTop();

                $settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            $this->objWriter->writeRawData($settingString);

            $this->objWriter->endElement();
        }
    }

    
    private function writeNamedRangeForPrintArea(Worksheet $pSheet, int $pSheetId = 0): void
    {
        
        if ($pSheet->getPageSetup()->isPrintAreaSet()) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm.Print_Area');
            $this->objWriter->writeAttribute('localSheetId', $pSheetId);

            
            $printArea = Coordinate::splitRange($pSheet->getPageSetup()->getPrintArea());

            $chunks = [];
            foreach ($printArea as $printAreaRect) {
                $printAreaRect[0] = Coordinate::absoluteReference($printAreaRect[0]);
                $printAreaRect[1] = Coordinate::absoluteReference($printAreaRect[1]);
                $chunks[] = '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
            }

            $this->objWriter->writeRawData(implode(',', $chunks));

            $this->objWriter->endElement();
        }
    }
}
