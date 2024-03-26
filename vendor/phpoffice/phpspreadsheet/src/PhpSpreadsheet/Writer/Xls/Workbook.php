<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;



































class Workbook extends BIFFwriter
{
    
    private $parser;

    
    private $biffSize;

    
    private $xfWriters = [];

    
    private $palette;

    
    private $codepage;

    
    private $countryCode;

    
    private $spreadsheet;

    
    private $fontWriters = [];

    
    private $addedFonts = [];

    
    private $numberFormats = [];

    
    private $addedNumberFormats = [];

    
    private $worksheetSizes = [];

    
    private $worksheetOffsets = [];

    
    private $stringTotal;

    
    private $stringUnique;

    
    private $stringTable;

    
    private $colors;

    
    private $escher;

    
    public function __construct(Spreadsheet $spreadsheet, &$str_total, &$str_unique, &$str_table, &$colors, Parser $parser)
    {
        
        parent::__construct();

        $this->parser = $parser;
        $this->biffSize = 0;
        $this->palette = [];
        $this->countryCode = -1;

        $this->stringTotal = &$str_total;
        $this->stringUnique = &$str_unique;
        $this->stringTable = &$str_table;
        $this->colors = &$colors;
        $this->setPaletteXl97();

        $this->spreadsheet = $spreadsheet;

        $this->codepage = 0x04B0;

        
        $countSheets = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $countSheets; ++$i) {
            $phpSheet = $spreadsheet->getSheet($i);

            $this->parser->setExtSheet($phpSheet->getTitle(), $i); 

            $supbook_index = 0x00;
            $ref = pack('vvv', $supbook_index, $i, $i);
            $this->parser->references[] = $ref; 

            
            if ($phpSheet->isTabColorSet()) {
                $this->addColor($phpSheet->getTabColor()->getRGB());
            }
        }
    }

    
    public function addXfWriter(Style $style, $isStyleXf = false)
    {
        $xfWriter = new Xf($style);
        $xfWriter->setIsStyleXf($isStyleXf);

        
        $fontIndex = $this->addFont($style->getFont());

        
        $xfWriter->setFontIndex($fontIndex);

        
        $xfWriter->setFgColor($this->addColor($style->getFill()->getStartColor()->getRGB()));
        $xfWriter->setBgColor($this->addColor($style->getFill()->getEndColor()->getRGB()));
        $xfWriter->setBottomColor($this->addColor($style->getBorders()->getBottom()->getColor()->getRGB()));
        $xfWriter->setTopColor($this->addColor($style->getBorders()->getTop()->getColor()->getRGB()));
        $xfWriter->setRightColor($this->addColor($style->getBorders()->getRight()->getColor()->getRGB()));
        $xfWriter->setLeftColor($this->addColor($style->getBorders()->getLeft()->getColor()->getRGB()));
        $xfWriter->setDiagColor($this->addColor($style->getBorders()->getDiagonal()->getColor()->getRGB()));

        
        if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
            $numberFormatHashCode = $style->getNumberFormat()->getHashCode();

            if (isset($this->addedNumberFormats[$numberFormatHashCode])) {
                $numberFormatIndex = $this->addedNumberFormats[$numberFormatHashCode];
            } else {
                $numberFormatIndex = 164 + count($this->numberFormats);
                $this->numberFormats[$numberFormatIndex] = $style->getNumberFormat();
                $this->addedNumberFormats[$numberFormatHashCode] = $numberFormatIndex;
            }
        } else {
            $numberFormatIndex = (int) $style->getNumberFormat()->getBuiltInFormatCode();
        }

        
        $xfWriter->setNumberFormatIndex($numberFormatIndex);

        $this->xfWriters[] = $xfWriter;

        return count($this->xfWriters) - 1;
    }

    
    public function addFont(\PhpOffice\PhpSpreadsheet\Style\Font $font)
    {
        $fontHashCode = $font->getHashCode();
        if (isset($this->addedFonts[$fontHashCode])) {
            $fontIndex = $this->addedFonts[$fontHashCode];
        } else {
            $countFonts = count($this->fontWriters);
            $fontIndex = ($countFonts < 4) ? $countFonts : $countFonts + 1;

            $fontWriter = new Font($font);
            $fontWriter->setColorIndex($this->addColor($font->getColor()->getRGB()));
            $this->fontWriters[] = $fontWriter;

            $this->addedFonts[$fontHashCode] = $fontIndex;
        }

        return $fontIndex;
    }

    
    private function addColor($rgb)
    {
        if (!isset($this->colors[$rgb])) {
            $color =
                [
                    hexdec(substr($rgb, 0, 2)),
                    hexdec(substr($rgb, 2, 2)),
                    hexdec(substr($rgb, 4)),
                    0,
                ];
            $colorIndex = array_search($color, $this->palette);
            if ($colorIndex) {
                $this->colors[$rgb] = $colorIndex;
            } else {
                if (count($this->colors) === 0) {
                    $lastColor = 7;
                } else {
                    $lastColor = end($this->colors);
                }
                if ($lastColor < 57) {
                    
                    $colorIndex = $lastColor + 1;
                    $this->palette[$colorIndex] = $color;
                    $this->colors[$rgb] = $colorIndex;
                } else {
                    
                    $colorIndex = 0;
                }
            }
        } else {
            
            $colorIndex = $this->colors[$rgb];
        }

        return $colorIndex;
    }

    
    private function setPaletteXl97(): void
    {
        $this->palette = [
            0x08 => [0x00, 0x00, 0x00, 0x00],
            0x09 => [0xff, 0xff, 0xff, 0x00],
            0x0A => [0xff, 0x00, 0x00, 0x00],
            0x0B => [0x00, 0xff, 0x00, 0x00],
            0x0C => [0x00, 0x00, 0xff, 0x00],
            0x0D => [0xff, 0xff, 0x00, 0x00],
            0x0E => [0xff, 0x00, 0xff, 0x00],
            0x0F => [0x00, 0xff, 0xff, 0x00],
            0x10 => [0x80, 0x00, 0x00, 0x00],
            0x11 => [0x00, 0x80, 0x00, 0x00],
            0x12 => [0x00, 0x00, 0x80, 0x00],
            0x13 => [0x80, 0x80, 0x00, 0x00],
            0x14 => [0x80, 0x00, 0x80, 0x00],
            0x15 => [0x00, 0x80, 0x80, 0x00],
            0x16 => [0xc0, 0xc0, 0xc0, 0x00],
            0x17 => [0x80, 0x80, 0x80, 0x00],
            0x18 => [0x99, 0x99, 0xff, 0x00],
            0x19 => [0x99, 0x33, 0x66, 0x00],
            0x1A => [0xff, 0xff, 0xcc, 0x00],
            0x1B => [0xcc, 0xff, 0xff, 0x00],
            0x1C => [0x66, 0x00, 0x66, 0x00],
            0x1D => [0xff, 0x80, 0x80, 0x00],
            0x1E => [0x00, 0x66, 0xcc, 0x00],
            0x1F => [0xcc, 0xcc, 0xff, 0x00],
            0x20 => [0x00, 0x00, 0x80, 0x00],
            0x21 => [0xff, 0x00, 0xff, 0x00],
            0x22 => [0xff, 0xff, 0x00, 0x00],
            0x23 => [0x00, 0xff, 0xff, 0x00],
            0x24 => [0x80, 0x00, 0x80, 0x00],
            0x25 => [0x80, 0x00, 0x00, 0x00],
            0x26 => [0x00, 0x80, 0x80, 0x00],
            0x27 => [0x00, 0x00, 0xff, 0x00],
            0x28 => [0x00, 0xcc, 0xff, 0x00],
            0x29 => [0xcc, 0xff, 0xff, 0x00],
            0x2A => [0xcc, 0xff, 0xcc, 0x00],
            0x2B => [0xff, 0xff, 0x99, 0x00],
            0x2C => [0x99, 0xcc, 0xff, 0x00],
            0x2D => [0xff, 0x99, 0xcc, 0x00],
            0x2E => [0xcc, 0x99, 0xff, 0x00],
            0x2F => [0xff, 0xcc, 0x99, 0x00],
            0x30 => [0x33, 0x66, 0xff, 0x00],
            0x31 => [0x33, 0xcc, 0xcc, 0x00],
            0x32 => [0x99, 0xcc, 0x00, 0x00],
            0x33 => [0xff, 0xcc, 0x00, 0x00],
            0x34 => [0xff, 0x99, 0x00, 0x00],
            0x35 => [0xff, 0x66, 0x00, 0x00],
            0x36 => [0x66, 0x66, 0x99, 0x00],
            0x37 => [0x96, 0x96, 0x96, 0x00],
            0x38 => [0x00, 0x33, 0x66, 0x00],
            0x39 => [0x33, 0x99, 0x66, 0x00],
            0x3A => [0x00, 0x33, 0x00, 0x00],
            0x3B => [0x33, 0x33, 0x00, 0x00],
            0x3C => [0x99, 0x33, 0x00, 0x00],
            0x3D => [0x99, 0x33, 0x66, 0x00],
            0x3E => [0x33, 0x33, 0x99, 0x00],
            0x3F => [0x33, 0x33, 0x33, 0x00],
        ];
    }

    
    public function writeWorkbook(array $pWorksheetSizes)
    {
        $this->worksheetSizes = $pWorksheetSizes;

        
        
        $total_worksheets = $this->spreadsheet->getSheetCount();

        
        $this->storeBof(0x0005);
        $this->writeCodepage();
        $this->writeWindow1();

        $this->writeDateMode();
        $this->writeAllFonts();
        $this->writeAllNumberFormats();
        $this->writeAllXfs();
        $this->writeAllStyles();
        $this->writePalette();

        
        $part3 = '';
        if ($this->countryCode !== -1) {
            $part3 .= $this->writeCountry();
        }
        $part3 .= $this->writeRecalcId();

        $part3 .= $this->writeSupbookInternal();
        
        $part3 .= $this->writeExternalsheetBiff8();
        $part3 .= $this->writeAllDefinedNamesBiff8();
        $part3 .= $this->writeMsoDrawingGroup();
        $part3 .= $this->writeSharedStringsTable();

        $part3 .= $this->writeEof();

        
        $this->calcSheetOffsets();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->writeBoundSheet($this->spreadsheet->getSheet($i), $this->worksheetOffsets[$i]);
        }

        
        $this->_data .= $part3;

        return $this->_data;
    }

    
    private function calcSheetOffsets(): void
    {
        $boundsheet_length = 10; 

        
        $offset = $this->_datasize;

        
        $total_worksheets = count($this->spreadsheet->getAllSheets());
        foreach ($this->spreadsheet->getWorksheetIterator() as $sheet) {
            $offset += $boundsheet_length + strlen(StringHelper::UTF8toBIFF8UnicodeShort($sheet->getTitle()));
        }

        
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->worksheetOffsets[$i] = $offset;
            $offset += $this->worksheetSizes[$i];
        }
        $this->biffSize = $offset;
    }

    
    private function writeAllFonts(): void
    {
        foreach ($this->fontWriters as $fontWriter) {
            $this->append($fontWriter->writeFont());
        }
    }

    
    private function writeAllNumberFormats(): void
    {
        foreach ($this->numberFormats as $numberFormatIndex => $numberFormat) {
            $this->writeNumberFormat($numberFormat->getFormatCode(), $numberFormatIndex);
        }
    }

    
    private function writeAllXfs(): void
    {
        foreach ($this->xfWriters as $xfWriter) {
            $this->append($xfWriter->writeXf());
        }
    }

    
    private function writeAllStyles(): void
    {
        $this->writeStyle();
    }

    private function parseDefinedNameValue(DefinedName $pDefinedName): string
    {
        $definedRange = $pDefinedName->getValue();
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF . '/mui',
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
                $newRange .= "\${$column}";
            }
            if (!empty($row)) {
                $newRange .= "\${$row}";
            }

            $definedRange = substr($definedRange, 0, $offset) . $newRange . substr($definedRange, $offset + $length);
        }

        return $definedRange;
    }

    
    private function writeAllDefinedNamesBiff8()
    {
        $chunk = '';

        
        $definedNames = $this->spreadsheet->getDefinedNames();
        if (count($definedNames) > 0) {
            
            foreach ($definedNames as $definedName) {
                $range = $this->parseDefinedNameValue($definedName);

                
                try {
                    $error = $this->parser->parse($range);
                    $formulaData = $this->parser->toReversePolish();

                    
                    if (isset($formulaData[0]) && ($formulaData[0] == "\x7A" || $formulaData[0] == "\x5A")) {
                        $formulaData = "\x3A" . substr($formulaData, 1);
                    }

                    if ($definedName->getLocalOnly()) {
                        
                        $scope = $this->spreadsheet->getIndex($definedName->getScope()) + 1;
                    } else {
                        
                        $scope = 0;
                    }
                    $chunk .= $this->writeData($this->writeDefinedNameBiff8($definedName->getName(), $formulaData, $scope, false));
                } catch (PhpSpreadsheetException $e) {
                    
                }
            }
        }

        
        $total_worksheets = $this->spreadsheet->getSheetCount();

        
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->spreadsheet->getSheet($i)->getPageSetup();
            
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin = Coordinate::columnIndexFromString($repeat[0]) - 1;
                $colmax = Coordinate::columnIndexFromString($repeat[1]) - 1;

                $repeat = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin = $repeat[0] - 1;
                $rowmax = $repeat[1] - 1;

                
                $formulaData = pack('Cv', 0x29, 0x17); 
                $formulaData .= pack('Cvvvvv', 0x3B, $i, 0, 65535, $colmin, $colmax); 
                $formulaData .= pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, 0, 255); 
                $formulaData .= pack('C', 0x10); 

                
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));

            
            } elseif ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = Coordinate::columnIndexFromString($repeat[0]) - 1;
                    $colmax = Coordinate::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }
                
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }

                
                $formulaData = pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, $colmin, $colmax);

                
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            }
        }

        
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->spreadsheet->getSheet($i)->getPageSetup();
            if ($sheetSetup->isPrintAreaSet()) {
                
                $printArea = Coordinate::splitRange($sheetSetup->getPrintArea());
                $countPrintArea = count($printArea);

                $formulaData = '';
                for ($j = 0; $j < $countPrintArea; ++$j) {
                    $printAreaRect = $printArea[$j]; 
                    $printAreaRect[0] = Coordinate::coordinateFromString($printAreaRect[0]);
                    $printAreaRect[1] = Coordinate::coordinateFromString($printAreaRect[1]);

                    $print_rowmin = $printAreaRect[0][1] - 1;
                    $print_rowmax = $printAreaRect[1][1] - 1;
                    $print_colmin = Coordinate::columnIndexFromString($printAreaRect[0][0]) - 1;
                    $print_colmax = Coordinate::columnIndexFromString($printAreaRect[1][0]) - 1;

                    
                    $formulaData .= pack('Cvvvvv', 0x3B, $i, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);

                    if ($j > 0) {
                        $formulaData .= pack('C', 0x10); 
                    }
                }

                
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x06), $formulaData, $i + 1, true));
            }
        }

        
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetAutoFilter = $this->spreadsheet->getSheet($i)->getAutoFilter();
            $autoFilterRange = $sheetAutoFilter->getRange();
            if (!empty($autoFilterRange)) {
                $rangeBounds = Coordinate::rangeBoundaries($autoFilterRange);

                
                $name = pack('C', 0x0D);

                $chunk .= $this->writeData($this->writeShortNameBiff8($name, $i + 1, $rangeBounds, true));
            }
        }

        return $chunk;
    }

    
    private function writeDefinedNameBiff8($name, $formulaData, $sheetIndex = 0, $isBuiltIn = false)
    {
        $record = 0x0018;

        
        $options = $isBuiltIn ? 0x20 : 0x00;

        
        $nlen = StringHelper::countCharacters($name);

        
        $name = substr(StringHelper::UTF8toBIFF8UnicodeLong($name), 2);

        
        $sz = strlen($formulaData);

        
        $data = pack('vCCvvvCCCC', $options, 0, $nlen, $sz, 0, $sheetIndex, 0, 0, 0, 0)
            . $name . $formulaData;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    
    private function writeShortNameBiff8($name, $sheetIndex, $rangeBounds, $isHidden = false)
    {
        $record = 0x0018;

        
        $options = ($isHidden ? 0x21 : 0x00);

        $extra = pack(
            'Cvvvvv',
            0x3B,
            $sheetIndex - 1,
            $rangeBounds[0][1] - 1,
            $rangeBounds[1][1] - 1,
            $rangeBounds[0][0] - 1,
            $rangeBounds[1][0] - 1
        );

        
        $sz = strlen($extra);

        
        $data = pack('vCCvvvCCCCC', $options, 0, 1, $sz, 0, $sheetIndex, 0, 0, 0, 0, 0)
            . $name . $extra;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    
    private function writeCodepage(): void
    {
        $record = 0x0042; 
        $length = 0x0002; 
        $cv = $this->codepage; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $cv);

        $this->append($header . $data);
    }

    
    private function writeWindow1(): void
    {
        $record = 0x003D; 
        $length = 0x0012; 

        $xWn = 0x0000; 
        $yWn = 0x0000; 
        $dxWn = 0x25BC; 
        $dyWn = 0x1572; 

        $grbit = 0x0038; 

        
        $ctabsel = 1; 

        $wTabRatio = 0x0258; 

        
        $itabFirst = 0; 
        $itabCur = $this->spreadsheet->getActiveSheetIndex(); 

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvvvvv', $xWn, $yWn, $dxWn, $dyWn, $grbit, $itabCur, $itabFirst, $ctabsel, $wTabRatio);
        $this->append($header . $data);
    }

    
    private function writeBoundSheet($sheet, $offset): void
    {
        $sheetname = $sheet->getTitle();
        $record = 0x0085; 

        
        switch ($sheet->getSheetState()) {
            case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VISIBLE:
                $ss = 0x00;

                break;
            case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN:
                $ss = 0x01;

                break;
            case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN:
                $ss = 0x02;

                break;
            default:
                $ss = 0x00;

                break;
        }

        
        $st = 0x00;

        $grbit = 0x0000; 

        $data = pack('VCC', $offset, $ss, $st);
        $data .= StringHelper::UTF8toBIFF8UnicodeShort($sheetname);

        $length = strlen($data);
        $header = pack('vv', $record, $length);
        $this->append($header . $data);
    }

    
    private function writeSupbookInternal()
    {
        $record = 0x01AE; 
        $length = 0x0004; 

        $header = pack('vv', $record, $length);
        $data = pack('vv', $this->spreadsheet->getSheetCount(), 0x0401);

        return $this->writeData($header . $data);
    }

    
    private function writeExternalsheetBiff8()
    {
        $totalReferences = count($this->parser->references);
        $record = 0x0017; 
        $length = 2 + 6 * $totalReferences; 

        $supbook_index = 0; 
        $header = pack('vv', $record, $length);
        $data = pack('v', $totalReferences);
        for ($i = 0; $i < $totalReferences; ++$i) {
            $data .= $this->parser->references[$i];
        }

        return $this->writeData($header . $data);
    }

    
    private function writeStyle(): void
    {
        $record = 0x0293; 
        $length = 0x0004; 

        $ixfe = 0x8000; 
        $BuiltIn = 0x00; 
        $iLevel = 0xff; 

        $header = pack('vv', $record, $length);
        $data = pack('vCC', $ixfe, $BuiltIn, $iLevel);
        $this->append($header . $data);
    }

    
    private function writeNumberFormat($format, $ifmt): void
    {
        $record = 0x041E; 

        $numberFormatString = StringHelper::UTF8toBIFF8UnicodeLong($format);
        $length = 2 + strlen($numberFormatString); 

        $header = pack('vv', $record, $length);
        $data = pack('v', $ifmt) . $numberFormatString;
        $this->append($header . $data);
    }

    
    private function writeDateMode(): void
    {
        $record = 0x0022; 
        $length = 0x0002; 

        $f1904 = (Date::getExcelCalendar() === Date::CALENDAR_MAC_1904)
            ? 1
            : 0; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $f1904);
        $this->append($header . $data);
    }

    
    private function writeCountry()
    {
        $record = 0x008C; 
        $length = 4; 

        $header = pack('vv', $record, $length);
        
        $data = pack('vv', $this->countryCode, $this->countryCode);

        return $this->writeData($header . $data);
    }

    
    private function writeRecalcId()
    {
        $record = 0x01C1; 
        $length = 8; 

        $header = pack('vv', $record, $length);

        
        $data = pack('VV', 0x000001C1, 0x00001E667);

        return $this->writeData($header . $data);
    }

    
    private function writePalette(): void
    {
        $aref = $this->palette;

        $record = 0x0092; 
        $length = 2 + 4 * count($aref); 
        $ccv = count($aref); 
        $data = ''; 

        
        foreach ($aref as $color) {
            foreach ($color as $byte) {
                $data .= pack('C', $byte);
            }
        }

        $header = pack('vvv', $record, $length, $ccv);
        $this->append($header . $data);
    }

    
    private function writeSharedStringsTable()
    {
        
        $continue_limit = 8224;

        
        $recordDatas = [];

        
        $recordData = pack('VV', $this->stringTotal, $this->stringUnique);

        
        foreach (array_keys($this->stringTable) as $string) {
            

            
            $headerinfo = unpack('vlength/Cencoding', $string);

            
            $encoding = $headerinfo['encoding'];

            
            $finished = false;

            while ($finished === false) {
                
                
                

                if (strlen($recordData) + strlen($string) <= $continue_limit) {
                    
                    $recordData .= $string;

                    if (strlen($recordData) + strlen($string) == $continue_limit) {
                        
                        $recordDatas[] = $recordData;
                        $recordData = '';
                    }

                    
                    $finished = true;
                } else {
                    
                    

                    
                    $space_remaining = $continue_limit - strlen($recordData);

                    
                    
                    
                    $min_space_needed = ($encoding == 1) ? 5 : 4;

                    
                    
                    
                    
                    

                    
                    if ($space_remaining < $min_space_needed) {
                        
                        $recordDatas[] = $recordData;

                        
                        $recordData = '';

                    
                    } else {
                        
                        $effective_space_remaining = $space_remaining;

                        
                        if ($encoding == 1 && (strlen($string) - $space_remaining) % 2 == 1) {
                            --$effective_space_remaining;
                        }

                        
                        $recordData .= substr($string, 0, $effective_space_remaining);

                        $string = substr($string, $effective_space_remaining); 
                        $recordDatas[] = $recordData;

                        
                        $recordData = pack('C', $encoding);
                    }
                }
            }
        }

        
        
        if (strlen($recordData) > 0) {
            $recordDatas[] = $recordData;
        }

        
        $chunk = '';
        foreach ($recordDatas as $i => $recordData) {
            
            $record = ($i == 0) ? 0x00FC : 0x003C;

            $header = pack('vv', $record, strlen($recordData));
            $data = $header . $recordData;

            $chunk .= $this->writeData($data);
        }

        return $chunk;
    }

    
    private function writeMsoDrawingGroup()
    {
        
        if (isset($this->escher)) {
            $writer = new Escher($this->escher);
            $data = $writer->close();

            $record = 0x00EB;
            $length = strlen($data);
            $header = pack('vv', $record, $length);

            return $this->writeData($header . $data);
        }

        return '';
    }

    
    public function getEscher()
    {
        return $this->escher;
    }

    
    public function setEscher(?\PhpOffice\PhpSpreadsheet\Shared\Escher $pValue = null): void
    {
        $this->escher = $pValue;
    }
}
