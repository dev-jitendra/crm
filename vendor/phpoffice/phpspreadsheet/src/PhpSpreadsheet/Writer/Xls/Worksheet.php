<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use GdImage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;



































class Worksheet extends BIFFwriter
{
    
    private $parser;

    
    private $xlsStringMaxLength;

    
    private $columnInfo;

    
    private $selection;

    
    private $activePane;

    
    private $outlineOn;

    
    private $outlineStyle;

    
    private $outlineBelow;

    
    private $outlineRight;

    
    private $stringTotal;

    
    private $stringUnique;

    
    private $stringTable;

    
    private $colors;

    
    private $firstRowIndex;

    
    private $lastRowIndex;

    
    private $firstColumnIndex;

    
    private $lastColumnIndex;

    
    public $phpSheet;

    
    private $countCellStyleXfs;

    
    private $escher;

    
    public $fontHashIndex;

    
    private $preCalculateFormulas;

    
    private $printHeaders;

    
    public function __construct(&$str_total, &$str_unique, &$str_table, &$colors, Parser $parser, $preCalculateFormulas, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $phpSheet)
    {
        
        parent::__construct();

        $this->preCalculateFormulas = $preCalculateFormulas;
        $this->stringTotal = &$str_total;
        $this->stringUnique = &$str_unique;
        $this->stringTable = &$str_table;
        $this->colors = &$colors;
        $this->parser = $parser;

        $this->phpSheet = $phpSheet;

        $this->xlsStringMaxLength = 255;
        $this->columnInfo = [];
        $this->selection = [0, 0, 0, 0];
        $this->activePane = 3;

        $this->printHeaders = 0;

        $this->outlineStyle = 0;
        $this->outlineBelow = 1;
        $this->outlineRight = 1;
        $this->outlineOn = 1;

        $this->fontHashIndex = [];

        
        $minR = 1;
        $minC = 'A';

        $maxR = $this->phpSheet->getHighestRow();
        $maxC = $this->phpSheet->getHighestColumn();

        
        $this->lastRowIndex = ($maxR > 65535) ? 65535 : $maxR;

        $this->firstColumnIndex = Coordinate::columnIndexFromString($minC);
        $this->lastColumnIndex = Coordinate::columnIndexFromString($maxC);


        if ($this->lastColumnIndex > 255) {
            $this->lastColumnIndex = 255;
        }

        $this->countCellStyleXfs = count($phpSheet->getParent()->getCellStyleXfCollection());
    }

    
    public function close(): void
    {
        $phpSheet = $this->phpSheet;

        
        $selectedCells = $this->phpSheet->getSelectedCells();
        $activeSheetIndex = $this->phpSheet->getParent()->getActiveSheetIndex();

        
        $this->storeBof(0x0010);

        
        $this->writePrintHeaders();

        
        $this->writePrintGridlines();

        
        $this->writeGridset();

        
        $phpSheet->calculateColumnWidths();

        
        if (($defaultWidth = $phpSheet->getDefaultColumnDimension()->getWidth()) < 0) {
            $defaultWidth = \PhpOffice\PhpSpreadsheet\Shared\Font::getDefaultColumnWidthByFont($phpSheet->getParent()->getDefaultStyle()->getFont());
        }

        $columnDimensions = $phpSheet->getColumnDimensions();
        $maxCol = $this->lastColumnIndex - 1;
        for ($i = 0; $i <= $maxCol; ++$i) {
            $hidden = 0;
            $level = 0;
            $xfIndex = 15; 

            $width = $defaultWidth;

            $columnLetter = Coordinate::stringFromColumnIndex($i + 1);
            if (isset($columnDimensions[$columnLetter])) {
                $columnDimension = $columnDimensions[$columnLetter];
                if ($columnDimension->getWidth() >= 0) {
                    $width = $columnDimension->getWidth();
                }
                $hidden = $columnDimension->getVisible() ? 0 : 1;
                $level = $columnDimension->getOutlineLevel();
                $xfIndex = $columnDimension->getXfIndex() + 15; 
            }

            
            
            
            
            
            
            
            $this->columnInfo[] = [$i, $i, $width, $xfIndex, $hidden, $level];
        }

        
        $this->writeGuts();

        
        $this->writeDefaultRowHeight();
        
        $this->writeWsbool();
        
        $this->writeBreaks();
        
        $this->writeHeader();
        
        $this->writeFooter();
        
        $this->writeHcenter();
        
        $this->writeVcenter();
        
        $this->writeMarginLeft();
        
        $this->writeMarginRight();
        
        $this->writeMarginTop();
        
        $this->writeMarginBottom();
        
        $this->writeSetup();
        
        $this->writeProtect();
        
        $this->writeScenProtect();
        
        $this->writeObjectProtect();
        
        $this->writePassword();
        
        $this->writeDefcol();

        
        if (!empty($this->columnInfo)) {
            $colcount = count($this->columnInfo);
            for ($i = 0; $i < $colcount; ++$i) {
                $this->writeColinfo($this->columnInfo[$i]);
            }
        }
        $autoFilterRange = $phpSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            
            $this->writeAutoFilterInfo();
        }

        
        $this->writeDimensions();

        
        foreach ($phpSheet->getRowDimensions() as $rowDimension) {
            $xfIndex = $rowDimension->getXfIndex() + 15; 
            $this->writeRow($rowDimension->getRowIndex() - 1, $rowDimension->getRowHeight(), $xfIndex, ($rowDimension->getVisible() ? '0' : '1'), $rowDimension->getOutlineLevel());
        }

        
        foreach ($phpSheet->getCoordinates() as $coordinate) {
            $cell = $phpSheet->getCell($coordinate);
            $row = $cell->getRow() - 1;
            $column = Coordinate::columnIndexFromString($cell->getColumn()) - 1;

            
            if ($row > 65535 || $column > 255) {
                throw new WriterException('Rows or columns overflow! Excel5 has limit to 65535 rows and 255 columns. Use XLSX instead.');
            }

            
            $xfIndex = $cell->getXfIndex() + 15; 

            $cVal = $cell->getValue();
            if ($cVal instanceof RichText) {
                $arrcRun = [];
                $str_len = StringHelper::countCharacters($cVal->getPlainText(), 'UTF-8');
                $str_pos = 0;
                $elements = $cVal->getRichTextElements();
                foreach ($elements as $element) {
                    
                    if ($element instanceof Run) {
                        $str_fontidx = $this->fontHashIndex[$element->getFont()->getHashCode()];
                    } else {
                        $str_fontidx = 0;
                    }
                    $arrcRun[] = ['strlen' => $str_pos, 'fontidx' => $str_fontidx];
                    
                    $str_pos += StringHelper::countCharacters($element->getText(), 'UTF-8');
                }
                $this->writeRichTextString($row, $column, $cVal->getPlainText(), $xfIndex, $arrcRun);
            } else {
                switch ($cell->getDatatype()) {
                    case DataType::TYPE_STRING:
                    case DataType::TYPE_NULL:
                        if ($cVal === '' || $cVal === null) {
                            $this->writeBlank($row, $column, $xfIndex);
                        } else {
                            $this->writeString($row, $column, $cVal, $xfIndex);
                        }

                        break;
                    case DataType::TYPE_NUMERIC:
                        $this->writeNumber($row, $column, $cVal, $xfIndex);

                        break;
                    case DataType::TYPE_FORMULA:
                        $calculatedValue = $this->preCalculateFormulas ?
                            $cell->getCalculatedValue() : null;
                        if (self::WRITE_FORMULA_EXCEPTION == $this->writeFormula($row, $column, $cVal, $xfIndex, $calculatedValue)) {
                            if ($calculatedValue === null) {
                                $calculatedValue = $cell->getCalculatedValue();
                            }
                            $calctype = gettype($calculatedValue);
                            switch ($calctype) {
                                case 'integer':
                                case 'double':
                                    $this->writeNumber($row, $column, $calculatedValue, $xfIndex);

                                    break;
                                case 'string':
                                    $this->writeString($row, $column, $calculatedValue, $xfIndex);

                                    break;
                                case 'boolean':
                                    $this->writeBoolErr($row, $column, $calculatedValue, 0, $xfIndex);

                                    break;
                                default:
                                    $this->writeString($row, $column, $cVal, $xfIndex);
                            }
                        }

                        break;
                    case DataType::TYPE_BOOL:
                        $this->writeBoolErr($row, $column, $cVal, 0, $xfIndex);

                        break;
                    case DataType::TYPE_ERROR:
                        $this->writeBoolErr($row, $column, self::mapErrorCode($cVal), 1, $xfIndex);

                        break;
                }
            }
        }

        
        $this->writeMsoDrawing();

        
        $this->phpSheet->getParent()->setActiveSheetIndex($activeSheetIndex);

        
        $this->writeWindow2();

        
        $this->writePageLayoutView();

        
        $this->writeZoom();
        if ($phpSheet->getFreezePane()) {
            $this->writePanes();
        }

        
        $this->phpSheet->setSelectedCells($selectedCells);

        
        $this->writeSelection();

        
        $this->writeMergedCells();

        
        foreach ($phpSheet->getHyperLinkCollection() as $coordinate => $hyperlink) {
            [$column, $row] = Coordinate::coordinateFromString($coordinate);

            $url = $hyperlink->getUrl();

            if (strpos($url, 'sheet:
                
                $url = str_replace('sheet:
            } elseif (preg_match('/^(http:|https:|ftp:|mailto:)/', $url)) {
                
            } else {
                
                $url = 'external:' . $url;
            }

            $this->writeUrl($row - 1, Coordinate::columnIndexFromString($column) - 1, $url);
        }

        $this->writeDataValidity();
        $this->writeSheetLayout();

        
        $this->writeSheetProtection();
        $this->writeRangeProtection();

        $arrConditionalStyles = $phpSheet->getConditionalStylesCollection();
        if (!empty($arrConditionalStyles)) {
            $arrConditional = [];
            
            
            $this->writeCFHeader();
            
            foreach ($arrConditionalStyles as $cellCoordinate => $conditionalStyles) {
                foreach ($conditionalStyles as $conditional) {
                    if (
                        $conditional->getConditionType() == Conditional::CONDITION_EXPRESSION
                        || $conditional->getConditionType() == Conditional::CONDITION_CELLIS
                    ) {
                        if (!isset($arrConditional[$conditional->getHashCode()])) {
                            
                            $arrConditional[$conditional->getHashCode()] = true;

                            
                            $this->writeCFRule($conditional);
                        }
                    }
                }
            }
        }

        $this->storeEof();
    }

    
    private function writeBIFF8CellRangeAddressFixed($range)
    {
        $explodes = explode(':', $range);

        
        $firstCell = $explodes[0];

        
        if (count($explodes) == 1) {
            $lastCell = $firstCell;
        } else {
            $lastCell = $explodes[1];
        }

        $firstCellCoordinates = Coordinate::coordinateFromString($firstCell); 
        $lastCellCoordinates = Coordinate::coordinateFromString($lastCell); 

        return pack('vvvv', $firstCellCoordinates[1] - 1, $lastCellCoordinates[1] - 1, Coordinate::columnIndexFromString($firstCellCoordinates[0]) - 1, Coordinate::columnIndexFromString($lastCellCoordinates[0]) - 1);
    }

    
    public function getData()
    {
        $buffer = 4096;

        
        if (isset($this->_data)) {
            $tmp = $this->_data;
            $this->_data = null;

            return $tmp;
        }

        
        return false;
    }

    
    public function printRowColHeaders($print = 1): void
    {
        $this->printHeaders = $print;
    }

    
    public function setOutline($visible = true, $symbols_below = true, $symbols_right = true, $auto_style = false): void
    {
        $this->outlineOn = $visible;
        $this->outlineBelow = $symbols_below;
        $this->outlineRight = $symbols_right;
        $this->outlineStyle = $auto_style;

        
        if ($this->outlineOn) {
            $this->outlineOn = 1;
        }
    }

    
    private function writeNumber($row, $col, $num, $xfIndex)
    {
        $record = 0x0203; 
        $length = 0x000E; 

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $row, $col, $xfIndex);
        $xl_double = pack('d', $num);
        if (self::getByteOrder()) { 
            $xl_double = strrev($xl_double);
        }

        $this->append($header . $data . $xl_double);

        return 0;
    }

    
    private function writeString($row, $col, $str, $xfIndex): void
    {
        $this->writeLabelSst($row, $col, $str, $xfIndex);
    }

    
    private function writeRichTextString($row, $col, $str, $xfIndex, $arrcRun): void
    {
        $record = 0x00FD; 
        $length = 0x000A; 
        $str = StringHelper::UTF8toBIFF8UnicodeShort($str, $arrcRun);

        
        if (!isset($this->stringTable[$str])) {
            $this->stringTable[$str] = $this->stringUnique++;
        }
        ++$this->stringTotal;

        $header = pack('vv', $record, $length);
        $data = pack('vvvV', $row, $col, $xfIndex, $this->stringTable[$str]);
        $this->append($header . $data);
    }

    
    private function writeLabelSst($row, $col, $str, $xfIndex): void
    {
        $record = 0x00FD; 
        $length = 0x000A; 

        $str = StringHelper::UTF8toBIFF8UnicodeLong($str);

        
        if (!isset($this->stringTable[$str])) {
            $this->stringTable[$str] = $this->stringUnique++;
        }
        ++$this->stringTotal;

        $header = pack('vv', $record, $length);
        $data = pack('vvvV', $row, $col, $xfIndex, $this->stringTable[$str]);
        $this->append($header . $data);
    }

    
    public function writeBlank($row, $col, $xfIndex)
    {
        $record = 0x0201; 
        $length = 0x0006; 

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $row, $col, $xfIndex);
        $this->append($header . $data);

        return 0;
    }

    
    private function writeBoolErr($row, $col, $value, $isError, $xfIndex)
    {
        $record = 0x0205;
        $length = 8;

        $header = pack('vv', $record, $length);
        $data = pack('vvvCC', $row, $col, $xfIndex, $value, $isError);
        $this->append($header . $data);

        return 0;
    }

    const WRITE_FORMULA_NORMAL = 0;
    const WRITE_FORMULA_ERRORS = -1;
    const WRITE_FORMULA_RANGE = -2;
    const WRITE_FORMULA_EXCEPTION = -3;

    
    private function writeFormula($row, $col, $formula, $xfIndex, $calculatedValue)
    {
        $record = 0x0006; 
        
        $stringValue = null;

        
        if (isset($calculatedValue)) {
            
            
            if (is_bool($calculatedValue)) {
                
                $num = pack('CCCvCv', 0x01, 0x00, (int) $calculatedValue, 0x00, 0x00, 0xFFFF);
            } elseif (is_int($calculatedValue) || is_float($calculatedValue)) {
                
                $num = pack('d', $calculatedValue);
            } elseif (is_string($calculatedValue)) {
                $errorCodes = DataType::getErrorCodes();
                if (isset($errorCodes[$calculatedValue])) {
                    
                    $num = pack('CCCvCv', 0x02, 0x00, self::mapErrorCode($calculatedValue), 0x00, 0x00, 0xFFFF);
                } elseif ($calculatedValue === '') {
                    
                    $num = pack('CCCvCv', 0x03, 0x00, 0x00, 0x00, 0x00, 0xFFFF);
                } else {
                    
                    $stringValue = $calculatedValue;
                    $num = pack('CCCvCv', 0x00, 0x00, 0x00, 0x00, 0x00, 0xFFFF);
                }
            } else {
                
                $num = pack('d', 0x00);
            }
        } else {
            $num = pack('d', 0x00);
        }

        $grbit = 0x03; 
        $unknown = 0x0000; 

        
        if ($formula[0] == '=') {
            $formula = substr($formula, 1);
        } else {
            
            $this->writeString($row, $col, 'Unrecognised character for formula', 0);

            return self::WRITE_FORMULA_ERRORS;
        }

        
        try {
            $error = $this->parser->parse($formula);
            $formula = $this->parser->toReversePolish();

            $formlen = strlen($formula); 
            $length = 0x16 + $formlen; 

            $header = pack('vv', $record, $length);

            $data = pack('vvv', $row, $col, $xfIndex)
                . $num
                . pack('vVv', $grbit, $unknown, $formlen);
            $this->append($header . $data . $formula);

            
            if ($stringValue !== null) {
                $this->writeStringRecord($stringValue);
            }

            return self::WRITE_FORMULA_NORMAL;
        } catch (PhpSpreadsheetException $e) {
            return self::WRITE_FORMULA_EXCEPTION;
        }
    }

    
    private function writeStringRecord($stringValue): void
    {
        $record = 0x0207; 
        $data = StringHelper::UTF8toBIFF8UnicodeLong($stringValue);

        $length = strlen($data);
        $header = pack('vv', $record, $length);

        $this->append($header . $data);
    }

    
    private function writeUrl($row, $col, $url)
    {
        
        return $this->writeUrlRange($row, $col, $row, $col, $url);
    }

    
    public function writeUrlRange($row1, $col1, $row2, $col2, $url)
    {
        
        if (preg_match('[^internal:]', $url)) {
            return $this->writeUrlInternal($row1, $col1, $row2, $col2, $url);
        }
        if (preg_match('[^external:]', $url)) {
            return $this->writeUrlExternal($row1, $col1, $row2, $col2, $url);
        }

        return $this->writeUrlWeb($row1, $col1, $row2, $col2, $url);
    }

    
    public function writeUrlWeb($row1, $col1, $row2, $col2, $url)
    {
        $record = 0x01B8; 
        $length = 0x00000; 

        
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
        $unknown2 = pack('H*', 'E0C9EA79F9BACE118C8200AA004BA90B');

        
        $options = pack('V', 0x03);

        
        $url = implode("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url = $url . "\0\0\0";

        
        $url_len = pack('V', strlen($url));

        
        $length = 0x34 + strlen($url);

        
        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $row1, $row2, $col1, $col2);

        
        $this->append($header . $data . $unknown1 . $options . $unknown2 . $url_len . $url);

        return 0;
    }

    
    public function writeUrlInternal($row1, $col1, $row2, $col2, $url)
    {
        $record = 0x01B8; 
        $length = 0x00000; 

        
        $url = preg_replace('/^internal:/', '', $url);

        
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');

        
        $options = pack('V', 0x08);

        
        $url .= "\0";

        
        $url_len = StringHelper::countCharacters($url);
        $url_len = pack('V', $url_len);

        $url = StringHelper::convertEncoding($url, 'UTF-16LE', 'UTF-8');

        
        $length = 0x24 + strlen($url);

        
        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $row1, $row2, $col1, $col2);

        
        $this->append($header . $data . $unknown1 . $options . $url_len . $url);

        return 0;
    }

    
    public function writeUrlExternal($row1, $col1, $row2, $col2, $url)
    {
        
        
        if (preg_match('[^external:\\\\]', $url)) {
            return; 
        }

        $record = 0x01B8; 
        $length = 0x00000; 

        
        
        $url = preg_replace('/^external:/', '', $url);
        $url = preg_replace('/\

        
        
        
        

        $absolute = 0x00; 
        if (preg_match('/^[A-Z]:/', $url)) {
            $absolute = 0x02; 
        }
        $link_type = 0x01 | $absolute;

        
        
        
        $dir_long = $url;
        if (preg_match('/\\#/', $url)) {
            $link_type |= 0x08;
        }

        
        $link_type = pack('V', $link_type);

        
        $up_count = preg_match_all('/\\.\\.\\\\/', $dir_long, $useless);
        $up_count = pack('v', $up_count);

        
        $dir_short = preg_replace('/\\.\\.\\\\/', '', $dir_long) . "\0";

        
        $dir_long = $dir_long . "\0";

        
        $dir_short_len = pack('V', strlen($dir_short));
        $dir_long_len = pack('V', strlen($dir_long));
        $stream_len = pack('V', 0); 

        
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
        $unknown2 = pack('H*', '0303000000000000C000000000000046');
        $unknown3 = pack('H*', 'FFFFADDE000000000000000000000000000000000000000');
        $unknown4 = pack('v', 0x03);

        
        $data = pack('vvvv', $row1, $row2, $col1, $col2) .
            $unknown1 .
            $link_type .
            $unknown2 .
            $up_count .
            $dir_short_len .
            $dir_short .
            $unknown3 .
            $stream_len; 

        
        $length = strlen($data);
        $header = pack('vv', $record, $length);

        
        $this->append($header . $data);

        return 0;
    }

    
    private function writeRow($row, $height, $xfIndex, $hidden = false, $level = 0): void
    {
        $record = 0x0208; 
        $length = 0x0010; 

        $colMic = 0x0000; 
        $colMac = 0x0000; 
        $irwMac = 0x0000; 
        $reserved = 0x0000; 
        $grbit = 0x0000; 
        $ixfe = $xfIndex;

        if ($height < 0) {
            $height = null;
        }

        
        if ($height != null) {
            $miyRw = $height * 20; 
        } else {
            $miyRw = 0xff; 
        }

        
        
        
        
        

        $grbit |= $level;
        if ($hidden) {
            $grbit |= 0x0030;
        }
        if ($height !== null) {
            $grbit |= 0x0040; 
        }
        if ($xfIndex !== 0xF) {
            $grbit |= 0x0080;
        }
        $grbit |= 0x0100;

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvvvv', $row, $colMic, $colMac, $miyRw, $irwMac, $reserved, $grbit, $ixfe);
        $this->append($header . $data);
    }

    
    private function writeDimensions(): void
    {
        $record = 0x0200; 

        $length = 0x000E;
        $data = pack('VVvvv', $this->firstRowIndex, $this->lastRowIndex + 1, $this->firstColumnIndex, $this->lastColumnIndex + 1, 0x0000); 

        $header = pack('vv', $record, $length);
        $this->append($header . $data);
    }

    
    private function writeWindow2(): void
    {
        $record = 0x023E; 
        $length = 0x0012;

        $grbit = 0x00B6; 
        $rwTop = 0x0000; 
        $colLeft = 0x0000; 

        
        $fDspFmla = 0; 
        $fDspGrid = $this->phpSheet->getShowGridlines() ? 1 : 0; 
        $fDspRwCol = $this->phpSheet->getShowRowColHeaders() ? 1 : 0; 
        $fFrozen = $this->phpSheet->getFreezePane() ? 1 : 0; 
        $fDspZeros = 1; 
        $fDefaultHdr = 1; 
        $fArabic = $this->phpSheet->getRightToLeft() ? 1 : 0; 
        $fDspGuts = $this->outlineOn; 
        $fFrozenNoSplit = 0; 
        
        $fSelected = ($this->phpSheet === $this->phpSheet->getParent()->getActiveSheet()) ? 1 : 0;
        $fPageBreakPreview = $this->phpSheet->getSheetView()->getView() === SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW;

        $grbit = $fDspFmla;
        $grbit |= $fDspGrid << 1;
        $grbit |= $fDspRwCol << 2;
        $grbit |= $fFrozen << 3;
        $grbit |= $fDspZeros << 4;
        $grbit |= $fDefaultHdr << 5;
        $grbit |= $fArabic << 6;
        $grbit |= $fDspGuts << 7;
        $grbit |= $fFrozenNoSplit << 8;
        $grbit |= $fSelected << 9; 
        $grbit |= $fSelected << 10; 
        $grbit |= $fPageBreakPreview << 11;

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $grbit, $rwTop, $colLeft);

        
        $rgbHdr = 0x0040; 
        $zoom_factor_page_break = ($fPageBreakPreview ? $this->phpSheet->getSheetView()->getZoomScale() : 0x0000);
        $zoom_factor_normal = $this->phpSheet->getSheetView()->getZoomScaleNormal();

        $data .= pack('vvvvV', $rgbHdr, 0x0000, $zoom_factor_page_break, $zoom_factor_normal, 0x00000000);

        $this->append($header . $data);
    }

    
    private function writeDefaultRowHeight(): void
    {
        $defaultRowHeight = $this->phpSheet->getDefaultRowDimension()->getRowHeight();

        if ($defaultRowHeight < 0) {
            return;
        }

        
        $defaultRowHeight = (int) 20 * $defaultRowHeight;

        $record = 0x0225; 
        $length = 0x0004; 

        $header = pack('vv', $record, $length);
        $data = pack('vv', 1, $defaultRowHeight);
        $this->append($header . $data);
    }

    
    private function writeDefcol(): void
    {
        $defaultColWidth = 8;

        $record = 0x0055; 
        $length = 0x0002; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $defaultColWidth);
        $this->append($header . $data);
    }

    
    private function writeColinfo($col_array): void
    {
        if (isset($col_array[0])) {
            $colFirst = $col_array[0];
        }
        if (isset($col_array[1])) {
            $colLast = $col_array[1];
        }
        if (isset($col_array[2])) {
            $coldx = $col_array[2];
        } else {
            $coldx = 8.43;
        }
        if (isset($col_array[3])) {
            $xfIndex = $col_array[3];
        } else {
            $xfIndex = 15;
        }
        if (isset($col_array[4])) {
            $grbit = $col_array[4];
        } else {
            $grbit = 0;
        }
        if (isset($col_array[5])) {
            $level = $col_array[5];
        } else {
            $level = 0;
        }
        $record = 0x007D; 
        $length = 0x000C; 

        $coldx *= 256; 

        $ixfe = $xfIndex;
        $reserved = 0x0000; 

        $level = max(0, min($level, 7));
        $grbit |= $level << 8;

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvv', $colFirst, $colLast, $coldx, $ixfe, $grbit, $reserved);
        $this->append($header . $data);
    }

    
    private function writeSelection(): void
    {
        
        $selectedCells = Coordinate::splitRange($this->phpSheet->getSelectedCells());
        $selectedCells = $selectedCells[0];
        if (count($selectedCells) == 2) {
            [$first, $last] = $selectedCells;
        } else {
            $first = $selectedCells[0];
            $last = $selectedCells[0];
        }

        [$colFirst, $rwFirst] = Coordinate::coordinateFromString($first);
        $colFirst = Coordinate::columnIndexFromString($colFirst) - 1; 
        --$rwFirst; 

        [$colLast, $rwLast] = Coordinate::coordinateFromString($last);
        $colLast = Coordinate::columnIndexFromString($colLast) - 1; 
        --$rwLast; 

        
        $colFirst = min($colFirst, 255);
        $colLast = min($colLast, 255);

        $rwFirst = min($rwFirst, 65535);
        $rwLast = min($rwLast, 65535);

        $record = 0x001D; 
        $length = 0x000F; 

        $pnn = $this->activePane; 
        $rwAct = $rwFirst; 
        $colAct = $colFirst; 
        $irefAct = 0; 
        $cref = 1; 

        if (!isset($rwLast)) {
            $rwLast = $rwFirst; 
        }
        if (!isset($colLast)) {
            $colLast = $colFirst; 
        }

        
        if ($rwFirst > $rwLast) {
            [$rwFirst, $rwLast] = [$rwLast, $rwFirst];
        }

        if ($colFirst > $colLast) {
            [$colFirst, $colLast] = [$colLast, $colFirst];
        }

        $header = pack('vv', $record, $length);
        $data = pack('CvvvvvvCC', $pnn, $rwAct, $colAct, $irefAct, $cref, $rwFirst, $rwLast, $colFirst, $colLast);
        $this->append($header . $data);
    }

    
    private function writeMergedCells(): void
    {
        $mergeCells = $this->phpSheet->getMergeCells();
        $countMergeCells = count($mergeCells);

        if ($countMergeCells == 0) {
            return;
        }

        
        $maxCountMergeCellsPerRecord = 1027;

        
        $record = 0x00E5;

        
        $i = 0;

        
        $j = 0;

        
        $recordData = '';

        
        foreach ($mergeCells as $mergeCell) {
            ++$i;
            ++$j;

            
            $range = Coordinate::splitRange($mergeCell);
            [$first, $last] = $range[0];
            [$firstColumn, $firstRow] = Coordinate::coordinateFromString($first);
            [$lastColumn, $lastRow] = Coordinate::coordinateFromString($last);

            $recordData .= pack('vvvv', $firstRow - 1, $lastRow - 1, Coordinate::columnIndexFromString($firstColumn) - 1, Coordinate::columnIndexFromString($lastColumn) - 1);

            
            if ($j == $maxCountMergeCellsPerRecord || $i == $countMergeCells) {
                $recordData = pack('v', $j) . $recordData;
                $length = strlen($recordData);
                $header = pack('vv', $record, $length);
                $this->append($header . $recordData);

                
                $recordData = '';
                $j = 0;
            }
        }
    }

    
    private function writeSheetLayout(): void
    {
        if (!$this->phpSheet->isTabColorSet()) {
            return;
        }

        $recordData = pack(
            'vvVVVvv',
            0x0862,
            0x0000, 
            0x00000000, 
            0x00000000, 
            0x00000014, 
            $this->colors[$this->phpSheet->getTabColor()->getRGB()], 
            0x0000        
        );

        $length = strlen($recordData);

        $record = 0x0862; 
        $header = pack('vv', $record, $length);
        $this->append($header . $recordData);
    }

    
    private function writeSheetProtection(): void
    {
        
        $record = 0x0867;

        
        $options = (int) !$this->phpSheet->getProtection()->getObjects()
            | (int) !$this->phpSheet->getProtection()->getScenarios() << 1
            | (int) !$this->phpSheet->getProtection()->getFormatCells() << 2
            | (int) !$this->phpSheet->getProtection()->getFormatColumns() << 3
            | (int) !$this->phpSheet->getProtection()->getFormatRows() << 4
            | (int) !$this->phpSheet->getProtection()->getInsertColumns() << 5
            | (int) !$this->phpSheet->getProtection()->getInsertRows() << 6
            | (int) !$this->phpSheet->getProtection()->getInsertHyperlinks() << 7
            | (int) !$this->phpSheet->getProtection()->getDeleteColumns() << 8
            | (int) !$this->phpSheet->getProtection()->getDeleteRows() << 9
            | (int) !$this->phpSheet->getProtection()->getSelectLockedCells() << 10
            | (int) !$this->phpSheet->getProtection()->getSort() << 11
            | (int) !$this->phpSheet->getProtection()->getAutoFilter() << 12
            | (int) !$this->phpSheet->getProtection()->getPivotTables() << 13
            | (int) !$this->phpSheet->getProtection()->getSelectUnlockedCells() << 14;

        
        $recordData = pack(
            'vVVCVVvv',
            0x0867, 
            0x0000, 
            0x0000, 
            0x00, 
            0x01000200, 
            0xFFFFFFFF, 
            $options, 
            0x0000 
        );

        $length = strlen($recordData);
        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    
    private function writeRangeProtection(): void
    {
        foreach ($this->phpSheet->getProtectedCells() as $range => $password) {
            
            $cellRanges = explode(' ', $range);
            $cref = count($cellRanges);

            $recordData = pack(
                'vvVVvCVvVv',
                0x0868,
                0x00,
                0x0000,
                0x0000,
                0x02,
                0x0,
                0x0000,
                $cref,
                0x0000,
                0x00
            );

            foreach ($cellRanges as $cellRange) {
                $recordData .= $this->writeBIFF8CellRangeAddressFixed($cellRange);
            }

            
            $recordData .= pack(
                'VV',
                0x0000,
                hexdec($password)
            );

            $recordData .= StringHelper::UTF8toBIFF8UnicodeLong('p' . md5($recordData));

            $length = strlen($recordData);

            $record = 0x0868; 
            $header = pack('vv', $record, $length);
            $this->append($header . $recordData);
        }
    }

    
    private function writePanes(): void
    {
        $panes = [];
        if ($this->phpSheet->getFreezePane()) {
            [$column, $row] = Coordinate::coordinateFromString($this->phpSheet->getFreezePane());
            $panes[0] = Coordinate::columnIndexFromString($column) - 1;
            $panes[1] = $row - 1;

            [$leftMostColumn, $topRow] = Coordinate::coordinateFromString($this->phpSheet->getTopLeftCell());
            
            $panes[2] = $topRow - 1;
            $panes[3] = Coordinate::columnIndexFromString($leftMostColumn) - 1;
        } else {
            
            return;
        }

        $x = $panes[0] ?? null;
        $y = $panes[1] ?? null;
        $rwTop = $panes[2] ?? null;
        $colLeft = $panes[3] ?? null;
        if (count($panes) > 4) { 
            $pnnAct = $panes[4];
        } else {
            $pnnAct = null;
        }
        $record = 0x0041; 
        $length = 0x000A; 

        
        if ($this->phpSheet->getFreezePane()) {
            
            if (!isset($rwTop)) {
                $rwTop = $y;
            }
            if (!isset($colLeft)) {
                $colLeft = $x;
            }
        } else {
            
            if (!isset($rwTop)) {
                $rwTop = 0;
            }
            if (!isset($colLeft)) {
                $colLeft = 0;
            }

            
            
            
            
            
            $y = 20 * $y + 255;
            $x = 113.879 * $x + 390;
        }

        
        
        
        if (!isset($pnnAct)) {
            if ($x != 0 && $y != 0) {
                $pnnAct = 0; 
            }
            if ($x != 0 && $y == 0) {
                $pnnAct = 1; 
            }
            if ($x == 0 && $y != 0) {
                $pnnAct = 2; 
            }
            if ($x == 0 && $y == 0) {
                $pnnAct = 3; 
            }
        }

        $this->activePane = $pnnAct; 

        $header = pack('vv', $record, $length);
        $data = pack('vvvvv', $x, $y, $rwTop, $colLeft, $pnnAct);
        $this->append($header . $data);
    }

    
    private function writeSetup(): void
    {
        $record = 0x00A1; 
        $length = 0x0022; 

        $iPaperSize = $this->phpSheet->getPageSetup()->getPaperSize(); 

        $iScale = $this->phpSheet->getPageSetup()->getScale() ?
            $this->phpSheet->getPageSetup()->getScale() : 100; 

        $iPageStart = 0x01; 
        $iFitWidth = (int) $this->phpSheet->getPageSetup()->getFitToWidth(); 
        $iFitHeight = (int) $this->phpSheet->getPageSetup()->getFitToHeight(); 
        $grbit = 0x00; 
        $iRes = 0x0258; 
        $iVRes = 0x0258; 

        $numHdr = $this->phpSheet->getPageMargins()->getHeader(); 

        $numFtr = $this->phpSheet->getPageMargins()->getFooter(); 
        $iCopies = 0x01; 

        
        $fLeftToRight = $this->phpSheet->getPageSetup()->getPageOrder() === PageSetup::PAGEORDER_DOWN_THEN_OVER
            ? 0x1 : 0x0;
        
        $fLandscape = ($this->phpSheet->getPageSetup()->getOrientation() == PageSetup::ORIENTATION_LANDSCAPE)
            ? 0x0 : 0x1;

        $fNoPls = 0x0; 
        $fNoColor = 0x0; 
        $fDraft = 0x0; 
        $fNotes = 0x0; 
        $fNoOrient = 0x0; 
        $fUsePage = 0x0; 

        $grbit = $fLeftToRight;
        $grbit |= $fLandscape << 1;
        $grbit |= $fNoPls << 2;
        $grbit |= $fNoColor << 3;
        $grbit |= $fDraft << 4;
        $grbit |= $fNotes << 5;
        $grbit |= $fNoOrient << 6;
        $grbit |= $fUsePage << 7;

        $numHdr = pack('d', $numHdr);
        $numFtr = pack('d', $numFtr);
        if (self::getByteOrder()) { 
            $numHdr = strrev($numHdr);
            $numFtr = strrev($numFtr);
        }

        $header = pack('vv', $record, $length);
        $data1 = pack('vvvvvvvv', $iPaperSize, $iScale, $iPageStart, $iFitWidth, $iFitHeight, $grbit, $iRes, $iVRes);
        $data2 = $numHdr . $numFtr;
        $data3 = pack('v', $iCopies);
        $this->append($header . $data1 . $data2 . $data3);
    }

    
    private function writeHeader(): void
    {
        $record = 0x0014; 

        

        $recordData = StringHelper::UTF8toBIFF8UnicodeLong($this->phpSheet->getHeaderFooter()->getOddHeader());
        $length = strlen($recordData);

        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    
    private function writeFooter(): void
    {
        $record = 0x0015; 

        

        $recordData = StringHelper::UTF8toBIFF8UnicodeLong($this->phpSheet->getHeaderFooter()->getOddFooter());
        $length = strlen($recordData);

        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    
    private function writeHcenter(): void
    {
        $record = 0x0083; 
        $length = 0x0002; 

        $fHCenter = $this->phpSheet->getPageSetup()->getHorizontalCentered() ? 1 : 0; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fHCenter);

        $this->append($header . $data);
    }

    
    private function writeVcenter(): void
    {
        $record = 0x0084; 
        $length = 0x0002; 

        $fVCenter = $this->phpSheet->getPageSetup()->getVerticalCentered() ? 1 : 0; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fVCenter);
        $this->append($header . $data);
    }

    
    private function writeMarginLeft(): void
    {
        $record = 0x0026; 
        $length = 0x0008; 

        $margin = $this->phpSheet->getPageMargins()->getLeft(); 

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { 
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    
    private function writeMarginRight(): void
    {
        $record = 0x0027; 
        $length = 0x0008; 

        $margin = $this->phpSheet->getPageMargins()->getRight(); 

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { 
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    
    private function writeMarginTop(): void
    {
        $record = 0x0028; 
        $length = 0x0008; 

        $margin = $this->phpSheet->getPageMargins()->getTop(); 

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { 
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    
    private function writeMarginBottom(): void
    {
        $record = 0x0029; 
        $length = 0x0008; 

        $margin = $this->phpSheet->getPageMargins()->getBottom(); 

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { 
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    
    private function writePrintHeaders(): void
    {
        $record = 0x002a; 
        $length = 0x0002; 

        $fPrintRwCol = $this->printHeaders; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fPrintRwCol);
        $this->append($header . $data);
    }

    
    private function writePrintGridlines(): void
    {
        $record = 0x002b; 
        $length = 0x0002; 

        $fPrintGrid = $this->phpSheet->getPrintGridlines() ? 1 : 0; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fPrintGrid);
        $this->append($header . $data);
    }

    
    private function writeGridset(): void
    {
        $record = 0x0082; 
        $length = 0x0002; 

        $fGridSet = !$this->phpSheet->getPrintGridlines(); 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fGridSet);
        $this->append($header . $data);
    }

    
    private function writeAutoFilterInfo(): void
    {
        $record = 0x009D; 
        $length = 0x0002; 

        $rangeBounds = Coordinate::rangeBoundaries($this->phpSheet->getAutoFilter()->getRange());
        $iNumFilters = 1 + $rangeBounds[1][0] - $rangeBounds[0][0];

        $header = pack('vv', $record, $length);
        $data = pack('v', $iNumFilters);
        $this->append($header . $data);
    }

    
    private function writeGuts(): void
    {
        $record = 0x0080; 
        $length = 0x0008; 

        $dxRwGut = 0x0000; 
        $dxColGut = 0x0000; 

        
        $maxRowOutlineLevel = 0;
        foreach ($this->phpSheet->getRowDimensions() as $rowDimension) {
            $maxRowOutlineLevel = max($maxRowOutlineLevel, $rowDimension->getOutlineLevel());
        }

        $col_level = 0;

        
        
        $colcount = count($this->columnInfo);
        for ($i = 0; $i < $colcount; ++$i) {
            $col_level = max($this->columnInfo[$i][5], $col_level);
        }

        
        $col_level = max(0, min($col_level, 7));

        
        if ($maxRowOutlineLevel) {
            ++$maxRowOutlineLevel;
        }
        if ($col_level) {
            ++$col_level;
        }

        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $dxRwGut, $dxColGut, $maxRowOutlineLevel, $col_level);

        $this->append($header . $data);
    }

    
    private function writeWsbool(): void
    {
        $record = 0x0081; 
        $length = 0x0002; 
        $grbit = 0x0000;

        
        
        
        
        $grbit |= 0x0001; 
        if ($this->outlineStyle) {
            $grbit |= 0x0020; 
        }
        if ($this->phpSheet->getShowSummaryBelow()) {
            $grbit |= 0x0040; 
        }
        if ($this->phpSheet->getShowSummaryRight()) {
            $grbit |= 0x0080; 
        }
        if ($this->phpSheet->getPageSetup()->getFitToPage()) {
            $grbit |= 0x0100; 
        }
        if ($this->outlineOn) {
            $grbit |= 0x0400; 
        }

        $header = pack('vv', $record, $length);
        $data = pack('v', $grbit);
        $this->append($header . $data);
    }

    
    private function writeBreaks(): void
    {
        
        $vbreaks = [];
        $hbreaks = [];

        foreach ($this->phpSheet->getBreaks() as $cell => $breakType) {
            
            $coordinates = Coordinate::coordinateFromString($cell);

            
            switch ($breakType) {
                case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_COLUMN:
                    
                    $vbreaks[] = Coordinate::columnIndexFromString($coordinates[0]) - 1;

                    break;
                case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW:
                    
                    $hbreaks[] = $coordinates[1];

                    break;
                case \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_NONE:
                default:
                    
                    break;
            }
        }

        
        if (!empty($hbreaks)) {
            
            sort($hbreaks, SORT_NUMERIC);
            if ($hbreaks[0] == 0) { 
                array_shift($hbreaks);
            }

            $record = 0x001b; 
            $cbrk = count($hbreaks); 
            $length = 2 + 6 * $cbrk; 

            $header = pack('vv', $record, $length);
            $data = pack('v', $cbrk);

            
            foreach ($hbreaks as $hbreak) {
                $data .= pack('vvv', $hbreak, 0x0000, 0x00ff);
            }

            $this->append($header . $data);
        }

        
        if (!empty($vbreaks)) {
            
            
            $vbreaks = array_slice($vbreaks, 0, 1000);

            
            sort($vbreaks, SORT_NUMERIC);
            if ($vbreaks[0] == 0) { 
                array_shift($vbreaks);
            }

            $record = 0x001a; 
            $cbrk = count($vbreaks); 
            $length = 2 + 6 * $cbrk; 

            $header = pack('vv', $record, $length);
            $data = pack('v', $cbrk);

            
            foreach ($vbreaks as $vbreak) {
                $data .= pack('vvv', $vbreak, 0x0000, 0xffff);
            }

            $this->append($header . $data);
        }
    }

    
    private function writeProtect(): void
    {
        
        if (!$this->phpSheet->getProtection()->getSheet()) {
            return;
        }

        $record = 0x0012; 
        $length = 0x0002; 

        $fLock = 1; 

        $header = pack('vv', $record, $length);
        $data = pack('v', $fLock);

        $this->append($header . $data);
    }

    
    private function writeScenProtect(): void
    {
        
        if (!$this->phpSheet->getProtection()->getSheet()) {
            return;
        }

        
        if (!$this->phpSheet->getProtection()->getScenarios()) {
            return;
        }

        $record = 0x00DD; 
        $length = 0x0002; 

        $header = pack('vv', $record, $length);
        $data = pack('v', 1);

        $this->append($header . $data);
    }

    
    private function writeObjectProtect(): void
    {
        
        if (!$this->phpSheet->getProtection()->getSheet()) {
            return;
        }

        
        if (!$this->phpSheet->getProtection()->getObjects()) {
            return;
        }

        $record = 0x0063; 
        $length = 0x0002; 

        $header = pack('vv', $record, $length);
        $data = pack('v', 1);

        $this->append($header . $data);
    }

    
    private function writePassword(): void
    {
        
        if (!$this->phpSheet->getProtection()->getSheet() || !$this->phpSheet->getProtection()->getPassword()) {
            return;
        }

        $record = 0x0013; 
        $length = 0x0002; 

        $wPassword = hexdec($this->phpSheet->getProtection()->getPassword()); 

        $header = pack('vv', $record, $length);
        $data = pack('v', $wPassword);

        $this->append($header . $data);
    }

    
    public function insertBitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1): void
    {
        $bitmap_array = (is_resource($bitmap) || $bitmap instanceof GdImage ? $this->processBitmapGd($bitmap) : $this->processBitmap($bitmap));
        [$width, $height, $size, $data] = $bitmap_array;

        
        $width *= $scale_x;
        $height *= $scale_y;

        
        $this->positionImage($col, $row, $x, $y, $width, $height);

        
        $record = 0x007f;
        $length = 8 + $size;
        $cf = 0x09;
        $env = 0x01;
        $lcb = $size;

        $header = pack('vvvvV', $record, $length, $cf, $env, $lcb);
        $this->append($header . $data);
    }

    
    public function positionImage($col_start, $row_start, $x1, $y1, $width, $height): void
    {
        
        $col_end = $col_start; 
        $row_end = $row_start; 

        
        if ($x1 >= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1))) {
            $x1 = 0;
        }
        if ($y1 >= Xls::sizeRow($this->phpSheet, $row_start + 1)) {
            $y1 = 0;
        }

        $width = $width + $x1 - 1;
        $height = $height + $y1 - 1;

        
        while ($width >= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1))) {
            $width -= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1));
            ++$col_end;
        }

        
        while ($height >= Xls::sizeRow($this->phpSheet, $row_end + 1)) {
            $height -= Xls::sizeRow($this->phpSheet, $row_end + 1);
            ++$row_end;
        }

        
        
        
        if (Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1)) == 0) {
            return;
        }
        if (Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1)) == 0) {
            return;
        }
        if (Xls::sizeRow($this->phpSheet, $row_start + 1) == 0) {
            return;
        }
        if (Xls::sizeRow($this->phpSheet, $row_end + 1) == 0) {
            return;
        }

        
        $x1 = $x1 / Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1)) * 1024;
        $y1 = $y1 / Xls::sizeRow($this->phpSheet, $row_start + 1) * 256;
        $x2 = $width / Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1)) * 1024; 
        $y2 = $height / Xls::sizeRow($this->phpSheet, $row_end + 1) * 256; 

        $this->writeObjPicture($col_start, $x1, $row_start, $y1, $col_end, $x2, $row_end, $y2);
    }

    
    private function writeObjPicture($colL, $dxL, $rwT, $dyT, $colR, $dxR, $rwB, $dyB): void
    {
        $record = 0x005d; 
        $length = 0x003c; 

        $cObj = 0x0001; 
        $OT = 0x0008; 
        $id = 0x0001; 
        $grbit = 0x0614; 

        $cbMacro = 0x0000; 
        $Reserved1 = 0x0000; 
        $Reserved2 = 0x0000; 

        $icvBack = 0x09; 
        $icvFore = 0x09; 
        $fls = 0x00; 
        $fAuto = 0x00; 
        $icv = 0x08; 
        $lns = 0xff; 
        $lnw = 0x01; 
        $fAutoB = 0x00; 
        $frs = 0x0000; 
        $cf = 0x0009; 
        $Reserved3 = 0x0000; 
        $cbPictFmla = 0x0000; 
        $Reserved4 = 0x0000; 
        $grbit2 = 0x0001; 
        $Reserved5 = 0x0000; 

        $header = pack('vv', $record, $length);
        $data = pack('V', $cObj);
        $data .= pack('v', $OT);
        $data .= pack('v', $id);
        $data .= pack('v', $grbit);
        $data .= pack('v', $colL);
        $data .= pack('v', $dxL);
        $data .= pack('v', $rwT);
        $data .= pack('v', $dyT);
        $data .= pack('v', $colR);
        $data .= pack('v', $dxR);
        $data .= pack('v', $rwB);
        $data .= pack('v', $dyB);
        $data .= pack('v', $cbMacro);
        $data .= pack('V', $Reserved1);
        $data .= pack('v', $Reserved2);
        $data .= pack('C', $icvBack);
        $data .= pack('C', $icvFore);
        $data .= pack('C', $fls);
        $data .= pack('C', $fAuto);
        $data .= pack('C', $icv);
        $data .= pack('C', $lns);
        $data .= pack('C', $lnw);
        $data .= pack('C', $fAutoB);
        $data .= pack('v', $frs);
        $data .= pack('V', $cf);
        $data .= pack('v', $Reserved3);
        $data .= pack('v', $cbPictFmla);
        $data .= pack('v', $Reserved4);
        $data .= pack('v', $grbit2);
        $data .= pack('V', $Reserved5);

        $this->append($header . $data);
    }

    
    public function processBitmapGd($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $data = pack('Vvvvv', 0x000c, $width, $height, 0x01, 0x18);
        for ($j = $height; --$j;) {
            for ($i = 0; $i < $width; ++$i) {
                $color = imagecolorsforindex($image, imagecolorat($image, $i, $j));
                foreach (['red', 'green', 'blue'] as $key) {
                    $color[$key] = $color[$key] + round((255 - $color[$key]) * $color['alpha'] / 127);
                }
                $data .= chr($color['blue']) . chr($color['green']) . chr($color['red']);
            }
            if (3 * $width % 4) {
                $data .= str_repeat("\x00", 4 - 3 * $width % 4);
            }
        }

        return [$width, $height, strlen($data), $data];
    }

    
    public function processBitmap($bitmap)
    {
        
        $bmp_fd = @fopen($bitmap, 'rb');
        if (!$bmp_fd) {
            throw new WriterException("Couldn't import $bitmap");
        }

        
        $data = fread($bmp_fd, filesize($bitmap));

        
        if (strlen($data) <= 0x36) {
            throw new WriterException("$bitmap doesn't contain enough data.\n");
        }

        
        $identity = unpack('A2ident', $data);
        if ($identity['ident'] != 'BM') {
            throw new WriterException("$bitmap doesn't appear to be a valid bitmap image.\n");
        }

        
        $data = substr($data, 2);

        
        
        
        $size_array = unpack('Vsa', substr($data, 0, 4));
        $size = $size_array['sa'];
        $data = substr($data, 4);
        $size -= 0x36; 
        $size += 0x0C; 

        
        $data = substr($data, 12);

        
        $width_and_height = unpack('V2', substr($data, 0, 8));
        $width = $width_and_height[1];
        $height = $width_and_height[2];
        $data = substr($data, 8);
        if ($width > 0xFFFF) {
            throw new WriterException("$bitmap: largest image width supported is 65k.\n");
        }
        if ($height > 0xFFFF) {
            throw new WriterException("$bitmap: largest image height supported is 65k.\n");
        }

        
        $planes_and_bitcount = unpack('v2', substr($data, 0, 4));
        $data = substr($data, 4);
        if ($planes_and_bitcount[2] != 24) { 
            throw new WriterException("$bitmap isn't a 24bit true color bitmap.\n");
        }
        if ($planes_and_bitcount[1] != 1) {
            throw new WriterException("$bitmap: only 1 plane supported in bitmap image.\n");
        }

        
        $compression = unpack('Vcomp', substr($data, 0, 4));
        $data = substr($data, 4);

        if ($compression['comp'] != 0) {
            throw new WriterException("$bitmap: compression not supported in bitmap image.\n");
        }

        
        $data = substr($data, 20);

        
        $header = pack('Vvvvv', 0x000c, $width, $height, 0x01, 0x18);
        $data = $header . $data;

        return [$width, $height, $size, $data];
    }

    
    private function writeZoom(): void
    {
        
        if ($this->phpSheet->getSheetView()->getZoomScale() == 100) {
            return;
        }

        $record = 0x00A0; 
        $length = 0x0004; 

        $header = pack('vv', $record, $length);
        $data = pack('vv', $this->phpSheet->getSheetView()->getZoomScale(), 100);
        $this->append($header . $data);
    }

    
    public function getEscher()
    {
        return $this->escher;
    }

    
    public function setEscher(?\PhpOffice\PhpSpreadsheet\Shared\Escher $pValue = null): void
    {
        $this->escher = $pValue;
    }

    
    private function writeMsoDrawing(): void
    {
        
        if (isset($this->escher)) {
            $writer = new Escher($this->escher);
            $data = $writer->close();
            $spOffsets = $writer->getSpOffsets();
            $spTypes = $writer->getSpTypes();
            

            
            $spOffsets[0] = 0;
            $nm = count($spOffsets) - 1; 
            for ($i = 1; $i <= $nm; ++$i) {
                
                $record = 0x00EC; 

                
                $dataChunk = substr($data, $spOffsets[$i - 1], $spOffsets[$i] - $spOffsets[$i - 1]);

                $length = strlen($dataChunk);
                $header = pack('vv', $record, $length);

                $this->append($header . $dataChunk);

                
                $record = 0x005D; 
                $objData = '';

                
                if ($spTypes[$i] == 0x00C9) {
                    
                    $objData .=
                        pack(
                            'vvvvvVVV',
                            0x0015, 
                            0x0012, 
                            0x0014, 
                            $i, 
                            0x2101, 
                            0, 
                            0, 
                            0  
                        );

                    
                    $objData .= pack('vv', 0x00C, 0x0014);
                    $objData .= pack('H*', '0000000000000000640001000A00000010000100');
                    
                    $objData .= pack('vv', 0x0013, 0x1FEE);
                    $objData .= pack('H*', '00000000010001030000020008005700');
                } else {
                    
                    $objData .=
                        pack(
                            'vvvvvVVV',
                            0x0015, 
                            0x0012, 
                            0x0008, 
                            $i, 
                            0x6011, 
                            0, 
                            0, 
                            0  
                        );
                }

                
                $objData .=
                    pack(
                        'vv',
                        0x0000, 
                        0x0000  
                    );

                $length = strlen($objData);
                $header = pack('vv', $record, $length);
                $this->append($header . $objData);
            }
        }
    }

    
    private function writeDataValidity(): void
    {
        
        $dataValidationCollection = $this->phpSheet->getDataValidationCollection();

        
        if (!empty($dataValidationCollection)) {
            
            $record = 0x01B2; 
            $length = 0x0012; 

            $grbit = 0x0000; 
            $horPos = 0x00000000; 
            $verPos = 0x00000000; 
            $objId = 0xFFFFFFFF; 

            $header = pack('vv', $record, $length);
            $data = pack('vVVVV', $grbit, $horPos, $verPos, $objId, count($dataValidationCollection));
            $this->append($header . $data);

            
            $record = 0x01BE; 

            foreach ($dataValidationCollection as $cellCoordinate => $dataValidation) {
                
                $data = '';

                
                $options = 0x00000000;

                
                $type = 0x00;
                switch ($dataValidation->getType()) {
                    case DataValidation::TYPE_NONE:
                        $type = 0x00;

                        break;
                    case DataValidation::TYPE_WHOLE:
                        $type = 0x01;

                        break;
                    case DataValidation::TYPE_DECIMAL:
                        $type = 0x02;

                        break;
                    case DataValidation::TYPE_LIST:
                        $type = 0x03;

                        break;
                    case DataValidation::TYPE_DATE:
                        $type = 0x04;

                        break;
                    case DataValidation::TYPE_TIME:
                        $type = 0x05;

                        break;
                    case DataValidation::TYPE_TEXTLENGTH:
                        $type = 0x06;

                        break;
                    case DataValidation::TYPE_CUSTOM:
                        $type = 0x07;

                        break;
                }

                $options |= $type << 0;

                
                $errorStyle = 0x00;
                switch ($dataValidation->getErrorStyle()) {
                    case DataValidation::STYLE_STOP:
                        $errorStyle = 0x00;

                        break;
                    case DataValidation::STYLE_WARNING:
                        $errorStyle = 0x01;

                        break;
                    case DataValidation::STYLE_INFORMATION:
                        $errorStyle = 0x02;

                        break;
                }

                $options |= $errorStyle << 4;

                
                if ($type == 0x03 && preg_match('/^\".*\"$/', $dataValidation->getFormula1())) {
                    $options |= 0x01 << 7;
                }

                
                $options |= $dataValidation->getAllowBlank() << 8;

                
                $options |= (!$dataValidation->getShowDropDown()) << 9;

                
                $options |= $dataValidation->getShowInputMessage() << 18;

                
                $options |= $dataValidation->getShowErrorMessage() << 19;

                
                $operator = 0x00;
                switch ($dataValidation->getOperator()) {
                    case DataValidation::OPERATOR_BETWEEN:
                        $operator = 0x00;

                        break;
                    case DataValidation::OPERATOR_NOTBETWEEN:
                        $operator = 0x01;

                        break;
                    case DataValidation::OPERATOR_EQUAL:
                        $operator = 0x02;

                        break;
                    case DataValidation::OPERATOR_NOTEQUAL:
                        $operator = 0x03;

                        break;
                    case DataValidation::OPERATOR_GREATERTHAN:
                        $operator = 0x04;

                        break;
                    case DataValidation::OPERATOR_LESSTHAN:
                        $operator = 0x05;

                        break;
                    case DataValidation::OPERATOR_GREATERTHANOREQUAL:
                        $operator = 0x06;

                        break;
                    case DataValidation::OPERATOR_LESSTHANOREQUAL:
                        $operator = 0x07;

                        break;
                }

                $options |= $operator << 20;

                $data = pack('V', $options);

                
                $promptTitle = $dataValidation->getPromptTitle() !== '' ?
                    $dataValidation->getPromptTitle() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($promptTitle);

                
                $errorTitle = $dataValidation->getErrorTitle() !== '' ?
                    $dataValidation->getErrorTitle() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($errorTitle);

                
                $prompt = $dataValidation->getPrompt() !== '' ?
                    $dataValidation->getPrompt() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($prompt);

                
                $error = $dataValidation->getError() !== '' ?
                    $dataValidation->getError() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($error);

                
                try {
                    $formula1 = $dataValidation->getFormula1();
                    if ($type == 0x03) { 
                        $formula1 = str_replace(',', chr(0), $formula1);
                    }
                    $this->parser->parse($formula1);
                    $formula1 = $this->parser->toReversePolish();
                    $sz1 = strlen($formula1);
                } catch (PhpSpreadsheetException $e) {
                    $sz1 = 0;
                    $formula1 = '';
                }
                $data .= pack('vv', $sz1, 0x0000);
                $data .= $formula1;

                
                try {
                    $formula2 = $dataValidation->getFormula2();
                    if ($formula2 === '') {
                        throw new WriterException('No formula2');
                    }
                    $this->parser->parse($formula2);
                    $formula2 = $this->parser->toReversePolish();
                    $sz2 = strlen($formula2);
                } catch (PhpSpreadsheetException $e) {
                    $sz2 = 0;
                    $formula2 = '';
                }
                $data .= pack('vv', $sz2, 0x0000);
                $data .= $formula2;

                
                $data .= pack('v', 0x0001);
                $data .= $this->writeBIFF8CellRangeAddressFixed($cellCoordinate);

                $length = strlen($data);
                $header = pack('vv', $record, $length);

                $this->append($header . $data);
            }
        }
    }

    
    private static function mapErrorCode($errorCode)
    {
        switch ($errorCode) {
            case '#NULL!':
                return 0x00;
            case '#DIV/0!':
                return 0x07;
            case '#VALUE!':
                return 0x0F;
            case '#REF!':
                return 0x17;
            case '#NAME?':
                return 0x1D;
            case '#NUM!':
                return 0x24;
            case '#N/A':
                return 0x2A;
        }

        return 0;
    }

    
    private function writePageLayoutView(): void
    {
        $record = 0x088B; 
        $length = 0x0010; 

        $rt = 0x088B; 
        $grbitFrt = 0x0000; 
        $reserved = 0x0000000000000000; 
        $wScalvePLV = $this->phpSheet->getSheetView()->getZoomScale(); 

        
        if ($this->phpSheet->getSheetView()->getView() == SheetView::SHEETVIEW_PAGE_LAYOUT) {
            $fPageLayoutView = 1;
        } else {
            $fPageLayoutView = 0;
        }
        $fRulerVisible = 0;
        $fWhitespaceHidden = 0;

        $grbit = $fPageLayoutView; 
        $grbit |= $fRulerVisible << 1;
        $grbit |= $fWhitespaceHidden << 3;

        $header = pack('vv', $record, $length);
        $data = pack('vvVVvv', $rt, $grbitFrt, 0x00000000, 0x00000000, $wScalvePLV, $grbit);
        $this->append($header . $data);
    }

    
    private function writeCFRule(Conditional $conditional): void
    {
        $record = 0x01B1; 

        
        
        if ($conditional->getConditionType() == Conditional::CONDITION_EXPRESSION) {
            $type = 0x02;
            $operatorType = 0x00;
        } elseif ($conditional->getConditionType() == Conditional::CONDITION_CELLIS) {
            $type = 0x01;

            switch ($conditional->getOperatorType()) {
                case Conditional::OPERATOR_NONE:
                    $operatorType = 0x00;

                    break;
                case Conditional::OPERATOR_EQUAL:
                    $operatorType = 0x03;

                    break;
                case Conditional::OPERATOR_GREATERTHAN:
                    $operatorType = 0x05;

                    break;
                case Conditional::OPERATOR_GREATERTHANOREQUAL:
                    $operatorType = 0x07;

                    break;
                case Conditional::OPERATOR_LESSTHAN:
                    $operatorType = 0x06;

                    break;
                case Conditional::OPERATOR_LESSTHANOREQUAL:
                    $operatorType = 0x08;

                    break;
                case Conditional::OPERATOR_NOTEQUAL:
                    $operatorType = 0x04;

                    break;
                case Conditional::OPERATOR_BETWEEN:
                    $operatorType = 0x01;

                    break;
                
            }
        }

        
        
        $arrConditions = $conditional->getConditions();
        $numConditions = count($arrConditions);
        if ($numConditions == 1) {
            $szValue1 = ($arrConditions[0] <= 65535 ? 3 : 0x0000);
            $szValue2 = 0x0000;
            $operand1 = pack('Cv', 0x1E, $arrConditions[0]);
            $operand2 = null;
        } elseif ($numConditions == 2 && ($conditional->getOperatorType() == Conditional::OPERATOR_BETWEEN)) {
            $szValue1 = ($arrConditions[0] <= 65535 ? 3 : 0x0000);
            $szValue2 = ($arrConditions[1] <= 65535 ? 3 : 0x0000);
            $operand1 = pack('Cv', 0x1E, $arrConditions[0]);
            $operand2 = pack('Cv', 0x1E, $arrConditions[1]);
        } else {
            $szValue1 = 0x0000;
            $szValue2 = 0x0000;
            $operand1 = null;
            $operand2 = null;
        }

        
        
        $bAlignHz = ($conditional->getStyle()->getAlignment()->getHorizontal() == null ? 1 : 0);
        $bAlignVt = ($conditional->getStyle()->getAlignment()->getVertical() == null ? 1 : 0);
        $bAlignWrapTx = ($conditional->getStyle()->getAlignment()->getWrapText() == false ? 1 : 0);
        $bTxRotation = ($conditional->getStyle()->getAlignment()->getTextRotation() == null ? 1 : 0);
        $bIndent = ($conditional->getStyle()->getAlignment()->getIndent() == 0 ? 1 : 0);
        $bShrinkToFit = ($conditional->getStyle()->getAlignment()->getShrinkToFit() == false ? 1 : 0);
        if ($bAlignHz == 0 || $bAlignVt == 0 || $bAlignWrapTx == 0 || $bTxRotation == 0 || $bIndent == 0 || $bShrinkToFit == 0) {
            $bFormatAlign = 1;
        } else {
            $bFormatAlign = 0;
        }
        
        $bProtLocked = ($conditional->getStyle()->getProtection()->getLocked() == null ? 1 : 0);
        $bProtHidden = ($conditional->getStyle()->getProtection()->getHidden() == null ? 1 : 0);
        if ($bProtLocked == 0 || $bProtHidden == 0) {
            $bFormatProt = 1;
        } else {
            $bFormatProt = 0;
        }
        
        $bBorderLeft = ($conditional->getStyle()->getBorders()->getLeft()->getColor()->getARGB() == Color::COLOR_BLACK
        && $conditional->getStyle()->getBorders()->getLeft()->getBorderStyle() == Border::BORDER_NONE ? 1 : 0);
        $bBorderRight = ($conditional->getStyle()->getBorders()->getRight()->getColor()->getARGB() == Color::COLOR_BLACK
        && $conditional->getStyle()->getBorders()->getRight()->getBorderStyle() == Border::BORDER_NONE ? 1 : 0);
        $bBorderTop = ($conditional->getStyle()->getBorders()->getTop()->getColor()->getARGB() == Color::COLOR_BLACK
        && $conditional->getStyle()->getBorders()->getTop()->getBorderStyle() == Border::BORDER_NONE ? 1 : 0);
        $bBorderBottom = ($conditional->getStyle()->getBorders()->getBottom()->getColor()->getARGB() == Color::COLOR_BLACK
        && $conditional->getStyle()->getBorders()->getBottom()->getBorderStyle() == Border::BORDER_NONE ? 1 : 0);
        if ($bBorderLeft == 0 || $bBorderRight == 0 || $bBorderTop == 0 || $bBorderBottom == 0) {
            $bFormatBorder = 1;
        } else {
            $bFormatBorder = 0;
        }
        
        $bFillStyle = ($conditional->getStyle()->getFill()->getFillType() == null ? 0 : 1);
        $bFillColor = ($conditional->getStyle()->getFill()->getStartColor()->getARGB() == null ? 0 : 1);
        $bFillColorBg = ($conditional->getStyle()->getFill()->getEndColor()->getARGB() == null ? 0 : 1);
        if ($bFillStyle == 0 || $bFillColor == 0 || $bFillColorBg == 0) {
            $bFormatFill = 1;
        } else {
            $bFormatFill = 0;
        }
        
        if (
            $conditional->getStyle()->getFont()->getName() != null
            || $conditional->getStyle()->getFont()->getSize() != null
            || $conditional->getStyle()->getFont()->getBold() != null
            || $conditional->getStyle()->getFont()->getItalic() != null
            || $conditional->getStyle()->getFont()->getSuperscript() != null
            || $conditional->getStyle()->getFont()->getSubscript() != null
            || $conditional->getStyle()->getFont()->getUnderline() != null
            || $conditional->getStyle()->getFont()->getStrikethrough() != null
            || $conditional->getStyle()->getFont()->getColor()->getARGB() != null
        ) {
            $bFormatFont = 1;
        } else {
            $bFormatFont = 0;
        }
        
        $flags = 0;
        $flags |= (1 == $bAlignHz ? 0x00000001 : 0);
        $flags |= (1 == $bAlignVt ? 0x00000002 : 0);
        $flags |= (1 == $bAlignWrapTx ? 0x00000004 : 0);
        $flags |= (1 == $bTxRotation ? 0x00000008 : 0);
        
        $flags |= (1 == 1 ? 0x00000010 : 0);
        $flags |= (1 == $bIndent ? 0x00000020 : 0);
        $flags |= (1 == $bShrinkToFit ? 0x00000040 : 0);
        
        $flags |= (1 == 1 ? 0x00000080 : 0);
        
        $flags |= (1 == $bProtLocked ? 0x00000100 : 0);
        $flags |= (1 == $bProtHidden ? 0x00000200 : 0);
        
        $flags |= (1 == $bBorderLeft ? 0x00000400 : 0);
        $flags |= (1 == $bBorderRight ? 0x00000800 : 0);
        $flags |= (1 == $bBorderTop ? 0x00001000 : 0);
        $flags |= (1 == $bBorderBottom ? 0x00002000 : 0);
        $flags |= (1 == 1 ? 0x00004000 : 0); 
        $flags |= (1 == 1 ? 0x00008000 : 0); 
        
        $flags |= (1 == $bFillStyle ? 0x00010000 : 0);
        $flags |= (1 == $bFillColor ? 0x00020000 : 0);
        $flags |= (1 == $bFillColorBg ? 0x00040000 : 0);
        $flags |= (1 == 1 ? 0x00380000 : 0);
        
        $flags |= (1 == $bFormatFont ? 0x04000000 : 0);
        
        $flags |= (1 == $bFormatAlign ? 0x08000000 : 0);
        
        $flags |= (1 == $bFormatBorder ? 0x10000000 : 0);
        
        $flags |= (1 == $bFormatFill ? 0x20000000 : 0);
        
        $flags |= (1 == $bFormatProt ? 0x40000000 : 0);
        
        $flags |= (1 == 0 ? 0x80000000 : 0);

        
        if ($bFormatFont == 1) {
            
            if ($conditional->getStyle()->getFont()->getName() == null) {
                $dataBlockFont = pack('VVVVVVVV', 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000);
                $dataBlockFont .= pack('VVVVVVVV', 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000);
            } else {
                $dataBlockFont = StringHelper::UTF8toBIFF8UnicodeLong($conditional->getStyle()->getFont()->getName());
            }
            
            if ($conditional->getStyle()->getFont()->getSize() == null) {
                $dataBlockFont .= pack('V', 20 * 11);
            } else {
                $dataBlockFont .= pack('V', 20 * $conditional->getStyle()->getFont()->getSize());
            }
            
            $dataBlockFont .= pack('V', 0);
            
            if ($conditional->getStyle()->getFont()->getBold() == true) {
                $dataBlockFont .= pack('v', 0x02BC);
            } else {
                $dataBlockFont .= pack('v', 0x0190);
            }
            
            if ($conditional->getStyle()->getFont()->getSubscript() == true) {
                $dataBlockFont .= pack('v', 0x02);
                $fontEscapement = 0;
            } elseif ($conditional->getStyle()->getFont()->getSuperscript() == true) {
                $dataBlockFont .= pack('v', 0x01);
                $fontEscapement = 0;
            } else {
                $dataBlockFont .= pack('v', 0x00);
                $fontEscapement = 1;
            }
            
            switch ($conditional->getStyle()->getFont()->getUnderline()) {
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE:
                    $dataBlockFont .= pack('C', 0x00);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE:
                    $dataBlockFont .= pack('C', 0x02);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING:
                    $dataBlockFont .= pack('C', 0x22);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE:
                    $dataBlockFont .= pack('C', 0x01);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLEACCOUNTING:
                    $dataBlockFont .= pack('C', 0x21);
                    $fontUnderline = 0;

                    break;
                default:
                    $dataBlockFont .= pack('C', 0x00);
                    $fontUnderline = 1;

                    break;
            }
            
            $dataBlockFont .= pack('vC', 0x0000, 0x00);
            
            switch ($conditional->getStyle()->getFont()->getColor()->getRGB()) {
                case '000000':
                    $colorIdx = 0x08;

                    break;
                case 'FFFFFF':
                    $colorIdx = 0x09;

                    break;
                case 'FF0000':
                    $colorIdx = 0x0A;

                    break;
                case '00FF00':
                    $colorIdx = 0x0B;

                    break;
                case '0000FF':
                    $colorIdx = 0x0C;

                    break;
                case 'FFFF00':
                    $colorIdx = 0x0D;

                    break;
                case 'FF00FF':
                    $colorIdx = 0x0E;

                    break;
                case '00FFFF':
                    $colorIdx = 0x0F;

                    break;
                case '800000':
                    $colorIdx = 0x10;

                    break;
                case '008000':
                    $colorIdx = 0x11;

                    break;
                case '000080':
                    $colorIdx = 0x12;

                    break;
                case '808000':
                    $colorIdx = 0x13;

                    break;
                case '800080':
                    $colorIdx = 0x14;

                    break;
                case '008080':
                    $colorIdx = 0x15;

                    break;
                case 'C0C0C0':
                    $colorIdx = 0x16;

                    break;
                case '808080':
                    $colorIdx = 0x17;

                    break;
                case '9999FF':
                    $colorIdx = 0x18;

                    break;
                case '993366':
                    $colorIdx = 0x19;

                    break;
                case 'FFFFCC':
                    $colorIdx = 0x1A;

                    break;
                case 'CCFFFF':
                    $colorIdx = 0x1B;

                    break;
                case '660066':
                    $colorIdx = 0x1C;

                    break;
                case 'FF8080':
                    $colorIdx = 0x1D;

                    break;
                case '0066CC':
                    $colorIdx = 0x1E;

                    break;
                case 'CCCCFF':
                    $colorIdx = 0x1F;

                    break;
                case '000080':
                    $colorIdx = 0x20;

                    break;
                case 'FF00FF':
                    $colorIdx = 0x21;

                    break;
                case 'FFFF00':
                    $colorIdx = 0x22;

                    break;
                case '00FFFF':
                    $colorIdx = 0x23;

                    break;
                case '800080':
                    $colorIdx = 0x24;

                    break;
                case '800000':
                    $colorIdx = 0x25;

                    break;
                case '008080':
                    $colorIdx = 0x26;

                    break;
                case '0000FF':
                    $colorIdx = 0x27;

                    break;
                case '00CCFF':
                    $colorIdx = 0x28;

                    break;
                case 'CCFFFF':
                    $colorIdx = 0x29;

                    break;
                case 'CCFFCC':
                    $colorIdx = 0x2A;

                    break;
                case 'FFFF99':
                    $colorIdx = 0x2B;

                    break;
                case '99CCFF':
                    $colorIdx = 0x2C;

                    break;
                case 'FF99CC':
                    $colorIdx = 0x2D;

                    break;
                case 'CC99FF':
                    $colorIdx = 0x2E;

                    break;
                case 'FFCC99':
                    $colorIdx = 0x2F;

                    break;
                case '3366FF':
                    $colorIdx = 0x30;

                    break;
                case '33CCCC':
                    $colorIdx = 0x31;

                    break;
                case '99CC00':
                    $colorIdx = 0x32;

                    break;
                case 'FFCC00':
                    $colorIdx = 0x33;

                    break;
                case 'FF9900':
                    $colorIdx = 0x34;

                    break;
                case 'FF6600':
                    $colorIdx = 0x35;

                    break;
                case '666699':
                    $colorIdx = 0x36;

                    break;
                case '969696':
                    $colorIdx = 0x37;

                    break;
                case '003366':
                    $colorIdx = 0x38;

                    break;
                case '339966':
                    $colorIdx = 0x39;

                    break;
                case '003300':
                    $colorIdx = 0x3A;

                    break;
                case '333300':
                    $colorIdx = 0x3B;

                    break;
                case '993300':
                    $colorIdx = 0x3C;

                    break;
                case '993366':
                    $colorIdx = 0x3D;

                    break;
                case '333399':
                    $colorIdx = 0x3E;

                    break;
                case '333333':
                    $colorIdx = 0x3F;

                    break;
                default:
                    $colorIdx = 0x00;

                    break;
            }
            $dataBlockFont .= pack('V', $colorIdx);
            
            $dataBlockFont .= pack('V', 0x00000000);
            
            $optionsFlags = 0;
            $optionsFlagsBold = ($conditional->getStyle()->getFont()->getBold() == null ? 1 : 0);
            $optionsFlags |= (1 == $optionsFlagsBold ? 0x00000002 : 0);
            $optionsFlags |= (1 == 1 ? 0x00000008 : 0);
            $optionsFlags |= (1 == 1 ? 0x00000010 : 0);
            $optionsFlags |= (1 == 0 ? 0x00000020 : 0);
            $optionsFlags |= (1 == 1 ? 0x00000080 : 0);
            $dataBlockFont .= pack('V', $optionsFlags);
            
            $dataBlockFont .= pack('V', $fontEscapement);
            
            $dataBlockFont .= pack('V', $fontUnderline);
            
            $dataBlockFont .= pack('V', 0x00000000);
            
            $dataBlockFont .= pack('V', 0x00000000);
            
            $dataBlockFont .= pack('VV', 0x00000000, 0x00000000);
            
            $dataBlockFont .= pack('v', 0x0001);
        }
        if ($bFormatAlign == 1) {
            $blockAlign = 0;
            
            switch ($conditional->getStyle()->getAlignment()->getHorizontal()) {
                case Alignment::HORIZONTAL_GENERAL:
                    $blockAlign = 0;

                    break;
                case Alignment::HORIZONTAL_LEFT:
                    $blockAlign = 1;

                    break;
                case Alignment::HORIZONTAL_RIGHT:
                    $blockAlign = 3;

                    break;
                case Alignment::HORIZONTAL_CENTER:
                    $blockAlign = 2;

                    break;
                case Alignment::HORIZONTAL_CENTER_CONTINUOUS:
                    $blockAlign = 6;

                    break;
                case Alignment::HORIZONTAL_JUSTIFY:
                    $blockAlign = 5;

                    break;
            }
            if ($conditional->getStyle()->getAlignment()->getWrapText() == true) {
                $blockAlign |= 1 << 3;
            } else {
                $blockAlign |= 0 << 3;
            }
            switch ($conditional->getStyle()->getAlignment()->getVertical()) {
                case Alignment::VERTICAL_BOTTOM:
                    $blockAlign = 2 << 4;

                    break;
                case Alignment::VERTICAL_TOP:
                    $blockAlign = 0 << 4;

                    break;
                case Alignment::VERTICAL_CENTER:
                    $blockAlign = 1 << 4;

                    break;
                case Alignment::VERTICAL_JUSTIFY:
                    $blockAlign = 3 << 4;

                    break;
            }
            $blockAlign |= 0 << 7;

            
            $blockRotation = $conditional->getStyle()->getAlignment()->getTextRotation();

            
            $blockIndent = $conditional->getStyle()->getAlignment()->getIndent();
            if ($conditional->getStyle()->getAlignment()->getShrinkToFit() == true) {
                $blockIndent |= 1 << 4;
            } else {
                $blockIndent |= 0 << 4;
            }
            $blockIndent |= 0 << 6;

            
            $blockIndentRelative = 255;

            $dataBlockAlign = pack('CCvvv', $blockAlign, $blockRotation, $blockIndent, $blockIndentRelative, 0x0000);
        }
        if ($bFormatBorder == 1) {
            $blockLineStyle = 0;
            switch ($conditional->getStyle()->getBorders()->getLeft()->getBorderStyle()) {
                case Border::BORDER_NONE:
                    $blockLineStyle |= 0x00;

                    break;
                case Border::BORDER_THIN:
                    $blockLineStyle |= 0x01;

                    break;
                case Border::BORDER_MEDIUM:
                    $blockLineStyle |= 0x02;

                    break;
                case Border::BORDER_DASHED:
                    $blockLineStyle |= 0x03;

                    break;
                case Border::BORDER_DOTTED:
                    $blockLineStyle |= 0x04;

                    break;
                case Border::BORDER_THICK:
                    $blockLineStyle |= 0x05;

                    break;
                case Border::BORDER_DOUBLE:
                    $blockLineStyle |= 0x06;

                    break;
                case Border::BORDER_HAIR:
                    $blockLineStyle |= 0x07;

                    break;
                case Border::BORDER_MEDIUMDASHED:
                    $blockLineStyle |= 0x08;

                    break;
                case Border::BORDER_DASHDOT:
                    $blockLineStyle |= 0x09;

                    break;
                case Border::BORDER_MEDIUMDASHDOT:
                    $blockLineStyle |= 0x0A;

                    break;
                case Border::BORDER_DASHDOTDOT:
                    $blockLineStyle |= 0x0B;

                    break;
                case Border::BORDER_MEDIUMDASHDOTDOT:
                    $blockLineStyle |= 0x0C;

                    break;
                case Border::BORDER_SLANTDASHDOT:
                    $blockLineStyle |= 0x0D;

                    break;
            }
            switch ($conditional->getStyle()->getBorders()->getRight()->getBorderStyle()) {
                case Border::BORDER_NONE:
                    $blockLineStyle |= 0x00 << 4;

                    break;
                case Border::BORDER_THIN:
                    $blockLineStyle |= 0x01 << 4;

                    break;
                case Border::BORDER_MEDIUM:
                    $blockLineStyle |= 0x02 << 4;

                    break;
                case Border::BORDER_DASHED:
                    $blockLineStyle |= 0x03 << 4;

                    break;
                case Border::BORDER_DOTTED:
                    $blockLineStyle |= 0x04 << 4;

                    break;
                case Border::BORDER_THICK:
                    $blockLineStyle |= 0x05 << 4;

                    break;
                case Border::BORDER_DOUBLE:
                    $blockLineStyle |= 0x06 << 4;

                    break;
                case Border::BORDER_HAIR:
                    $blockLineStyle |= 0x07 << 4;

                    break;
                case Border::BORDER_MEDIUMDASHED:
                    $blockLineStyle |= 0x08 << 4;

                    break;
                case Border::BORDER_DASHDOT:
                    $blockLineStyle |= 0x09 << 4;

                    break;
                case Border::BORDER_MEDIUMDASHDOT:
                    $blockLineStyle |= 0x0A << 4;

                    break;
                case Border::BORDER_DASHDOTDOT:
                    $blockLineStyle |= 0x0B << 4;

                    break;
                case Border::BORDER_MEDIUMDASHDOTDOT:
                    $blockLineStyle |= 0x0C << 4;

                    break;
                case Border::BORDER_SLANTDASHDOT:
                    $blockLineStyle |= 0x0D << 4;

                    break;
            }
            switch ($conditional->getStyle()->getBorders()->getTop()->getBorderStyle()) {
                case Border::BORDER_NONE:
                    $blockLineStyle |= 0x00 << 8;

                    break;
                case Border::BORDER_THIN:
                    $blockLineStyle |= 0x01 << 8;

                    break;
                case Border::BORDER_MEDIUM:
                    $blockLineStyle |= 0x02 << 8;

                    break;
                case Border::BORDER_DASHED:
                    $blockLineStyle |= 0x03 << 8;

                    break;
                case Border::BORDER_DOTTED:
                    $blockLineStyle |= 0x04 << 8;

                    break;
                case Border::BORDER_THICK:
                    $blockLineStyle |= 0x05 << 8;

                    break;
                case Border::BORDER_DOUBLE:
                    $blockLineStyle |= 0x06 << 8;

                    break;
                case Border::BORDER_HAIR:
                    $blockLineStyle |= 0x07 << 8;

                    break;
                case Border::BORDER_MEDIUMDASHED:
                    $blockLineStyle |= 0x08 << 8;

                    break;
                case Border::BORDER_DASHDOT:
                    $blockLineStyle |= 0x09 << 8;

                    break;
                case Border::BORDER_MEDIUMDASHDOT:
                    $blockLineStyle |= 0x0A << 8;

                    break;
                case Border::BORDER_DASHDOTDOT:
                    $blockLineStyle |= 0x0B << 8;

                    break;
                case Border::BORDER_MEDIUMDASHDOTDOT:
                    $blockLineStyle |= 0x0C << 8;

                    break;
                case Border::BORDER_SLANTDASHDOT:
                    $blockLineStyle |= 0x0D << 8;

                    break;
            }
            switch ($conditional->getStyle()->getBorders()->getBottom()->getBorderStyle()) {
                case Border::BORDER_NONE:
                    $blockLineStyle |= 0x00 << 12;

                    break;
                case Border::BORDER_THIN:
                    $blockLineStyle |= 0x01 << 12;

                    break;
                case Border::BORDER_MEDIUM:
                    $blockLineStyle |= 0x02 << 12;

                    break;
                case Border::BORDER_DASHED:
                    $blockLineStyle |= 0x03 << 12;

                    break;
                case Border::BORDER_DOTTED:
                    $blockLineStyle |= 0x04 << 12;

                    break;
                case Border::BORDER_THICK:
                    $blockLineStyle |= 0x05 << 12;

                    break;
                case Border::BORDER_DOUBLE:
                    $blockLineStyle |= 0x06 << 12;

                    break;
                case Border::BORDER_HAIR:
                    $blockLineStyle |= 0x07 << 12;

                    break;
                case Border::BORDER_MEDIUMDASHED:
                    $blockLineStyle |= 0x08 << 12;

                    break;
                case Border::BORDER_DASHDOT:
                    $blockLineStyle |= 0x09 << 12;

                    break;
                case Border::BORDER_MEDIUMDASHDOT:
                    $blockLineStyle |= 0x0A << 12;

                    break;
                case Border::BORDER_DASHDOTDOT:
                    $blockLineStyle |= 0x0B << 12;

                    break;
                case Border::BORDER_MEDIUMDASHDOTDOT:
                    $blockLineStyle |= 0x0C << 12;

                    break;
                case Border::BORDER_SLANTDASHDOT:
                    $blockLineStyle |= 0x0D << 12;

                    break;
            }

            
            
            
            
            $blockColor = 0;
            
            
            
            switch ($conditional->getStyle()->getBorders()->getDiagonal()->getBorderStyle()) {
                case Border::BORDER_NONE:
                    $blockColor |= 0x00 << 21;

                    break;
                case Border::BORDER_THIN:
                    $blockColor |= 0x01 << 21;

                    break;
                case Border::BORDER_MEDIUM:
                    $blockColor |= 0x02 << 21;

                    break;
                case Border::BORDER_DASHED:
                    $blockColor |= 0x03 << 21;

                    break;
                case Border::BORDER_DOTTED:
                    $blockColor |= 0x04 << 21;

                    break;
                case Border::BORDER_THICK:
                    $blockColor |= 0x05 << 21;

                    break;
                case Border::BORDER_DOUBLE:
                    $blockColor |= 0x06 << 21;

                    break;
                case Border::BORDER_HAIR:
                    $blockColor |= 0x07 << 21;

                    break;
                case Border::BORDER_MEDIUMDASHED:
                    $blockColor |= 0x08 << 21;

                    break;
                case Border::BORDER_DASHDOT:
                    $blockColor |= 0x09 << 21;

                    break;
                case Border::BORDER_MEDIUMDASHDOT:
                    $blockColor |= 0x0A << 21;

                    break;
                case Border::BORDER_DASHDOTDOT:
                    $blockColor |= 0x0B << 21;

                    break;
                case Border::BORDER_MEDIUMDASHDOTDOT:
                    $blockColor |= 0x0C << 21;

                    break;
                case Border::BORDER_SLANTDASHDOT:
                    $blockColor |= 0x0D << 21;

                    break;
            }
            $dataBlockBorder = pack('vv', $blockLineStyle, $blockColor);
        }
        if ($bFormatFill == 1) {
            
            $blockFillPatternStyle = 0;
            switch ($conditional->getStyle()->getFill()->getFillType()) {
                case Fill::FILL_NONE:
                    $blockFillPatternStyle = 0x00;

                    break;
                case Fill::FILL_SOLID:
                    $blockFillPatternStyle = 0x01;

                    break;
                case Fill::FILL_PATTERN_MEDIUMGRAY:
                    $blockFillPatternStyle = 0x02;

                    break;
                case Fill::FILL_PATTERN_DARKGRAY:
                    $blockFillPatternStyle = 0x03;

                    break;
                case Fill::FILL_PATTERN_LIGHTGRAY:
                    $blockFillPatternStyle = 0x04;

                    break;
                case Fill::FILL_PATTERN_DARKHORIZONTAL:
                    $blockFillPatternStyle = 0x05;

                    break;
                case Fill::FILL_PATTERN_DARKVERTICAL:
                    $blockFillPatternStyle = 0x06;

                    break;
                case Fill::FILL_PATTERN_DARKDOWN:
                    $blockFillPatternStyle = 0x07;

                    break;
                case Fill::FILL_PATTERN_DARKUP:
                    $blockFillPatternStyle = 0x08;

                    break;
                case Fill::FILL_PATTERN_DARKGRID:
                    $blockFillPatternStyle = 0x09;

                    break;
                case Fill::FILL_PATTERN_DARKTRELLIS:
                    $blockFillPatternStyle = 0x0A;

                    break;
                case Fill::FILL_PATTERN_LIGHTHORIZONTAL:
                    $blockFillPatternStyle = 0x0B;

                    break;
                case Fill::FILL_PATTERN_LIGHTVERTICAL:
                    $blockFillPatternStyle = 0x0C;

                    break;
                case Fill::FILL_PATTERN_LIGHTDOWN:
                    $blockFillPatternStyle = 0x0D;

                    break;
                case Fill::FILL_PATTERN_LIGHTUP:
                    $blockFillPatternStyle = 0x0E;

                    break;
                case Fill::FILL_PATTERN_LIGHTGRID:
                    $blockFillPatternStyle = 0x0F;

                    break;
                case Fill::FILL_PATTERN_LIGHTTRELLIS:
                    $blockFillPatternStyle = 0x10;

                    break;
                case Fill::FILL_PATTERN_GRAY125:
                    $blockFillPatternStyle = 0x11;

                    break;
                case Fill::FILL_PATTERN_GRAY0625:
                    $blockFillPatternStyle = 0x12;

                    break;
                case Fill::FILL_GRADIENT_LINEAR:
                    $blockFillPatternStyle = 0x00;

                    break; 
                case Fill::FILL_GRADIENT_PATH:
                    $blockFillPatternStyle = 0x00;

                    break; 
                default:
                    $blockFillPatternStyle = 0x00;

                    break;
            }
            
            switch ($conditional->getStyle()->getFill()->getStartColor()->getRGB()) {
                case '000000':
                    $colorIdxBg = 0x08;

                    break;
                case 'FFFFFF':
                    $colorIdxBg = 0x09;

                    break;
                case 'FF0000':
                    $colorIdxBg = 0x0A;

                    break;
                case '00FF00':
                    $colorIdxBg = 0x0B;

                    break;
                case '0000FF':
                    $colorIdxBg = 0x0C;

                    break;
                case 'FFFF00':
                    $colorIdxBg = 0x0D;

                    break;
                case 'FF00FF':
                    $colorIdxBg = 0x0E;

                    break;
                case '00FFFF':
                    $colorIdxBg = 0x0F;

                    break;
                case '800000':
                    $colorIdxBg = 0x10;

                    break;
                case '008000':
                    $colorIdxBg = 0x11;

                    break;
                case '000080':
                    $colorIdxBg = 0x12;

                    break;
                case '808000':
                    $colorIdxBg = 0x13;

                    break;
                case '800080':
                    $colorIdxBg = 0x14;

                    break;
                case '008080':
                    $colorIdxBg = 0x15;

                    break;
                case 'C0C0C0':
                    $colorIdxBg = 0x16;

                    break;
                case '808080':
                    $colorIdxBg = 0x17;

                    break;
                case '9999FF':
                    $colorIdxBg = 0x18;

                    break;
                case '993366':
                    $colorIdxBg = 0x19;

                    break;
                case 'FFFFCC':
                    $colorIdxBg = 0x1A;

                    break;
                case 'CCFFFF':
                    $colorIdxBg = 0x1B;

                    break;
                case '660066':
                    $colorIdxBg = 0x1C;

                    break;
                case 'FF8080':
                    $colorIdxBg = 0x1D;

                    break;
                case '0066CC':
                    $colorIdxBg = 0x1E;

                    break;
                case 'CCCCFF':
                    $colorIdxBg = 0x1F;

                    break;
                case '000080':
                    $colorIdxBg = 0x20;

                    break;
                case 'FF00FF':
                    $colorIdxBg = 0x21;

                    break;
                case 'FFFF00':
                    $colorIdxBg = 0x22;

                    break;
                case '00FFFF':
                    $colorIdxBg = 0x23;

                    break;
                case '800080':
                    $colorIdxBg = 0x24;

                    break;
                case '800000':
                    $colorIdxBg = 0x25;

                    break;
                case '008080':
                    $colorIdxBg = 0x26;

                    break;
                case '0000FF':
                    $colorIdxBg = 0x27;

                    break;
                case '00CCFF':
                    $colorIdxBg = 0x28;

                    break;
                case 'CCFFFF':
                    $colorIdxBg = 0x29;

                    break;
                case 'CCFFCC':
                    $colorIdxBg = 0x2A;

                    break;
                case 'FFFF99':
                    $colorIdxBg = 0x2B;

                    break;
                case '99CCFF':
                    $colorIdxBg = 0x2C;

                    break;
                case 'FF99CC':
                    $colorIdxBg = 0x2D;

                    break;
                case 'CC99FF':
                    $colorIdxBg = 0x2E;

                    break;
                case 'FFCC99':
                    $colorIdxBg = 0x2F;

                    break;
                case '3366FF':
                    $colorIdxBg = 0x30;

                    break;
                case '33CCCC':
                    $colorIdxBg = 0x31;

                    break;
                case '99CC00':
                    $colorIdxBg = 0x32;

                    break;
                case 'FFCC00':
                    $colorIdxBg = 0x33;

                    break;
                case 'FF9900':
                    $colorIdxBg = 0x34;

                    break;
                case 'FF6600':
                    $colorIdxBg = 0x35;

                    break;
                case '666699':
                    $colorIdxBg = 0x36;

                    break;
                case '969696':
                    $colorIdxBg = 0x37;

                    break;
                case '003366':
                    $colorIdxBg = 0x38;

                    break;
                case '339966':
                    $colorIdxBg = 0x39;

                    break;
                case '003300':
                    $colorIdxBg = 0x3A;

                    break;
                case '333300':
                    $colorIdxBg = 0x3B;

                    break;
                case '993300':
                    $colorIdxBg = 0x3C;

                    break;
                case '993366':
                    $colorIdxBg = 0x3D;

                    break;
                case '333399':
                    $colorIdxBg = 0x3E;

                    break;
                case '333333':
                    $colorIdxBg = 0x3F;

                    break;
                default:
                    $colorIdxBg = 0x41;

                    break;
            }
            
            switch ($conditional->getStyle()->getFill()->getEndColor()->getRGB()) {
                case '000000':
                    $colorIdxFg = 0x08;

                    break;
                case 'FFFFFF':
                    $colorIdxFg = 0x09;

                    break;
                case 'FF0000':
                    $colorIdxFg = 0x0A;

                    break;
                case '00FF00':
                    $colorIdxFg = 0x0B;

                    break;
                case '0000FF':
                    $colorIdxFg = 0x0C;

                    break;
                case 'FFFF00':
                    $colorIdxFg = 0x0D;

                    break;
                case 'FF00FF':
                    $colorIdxFg = 0x0E;

                    break;
                case '00FFFF':
                    $colorIdxFg = 0x0F;

                    break;
                case '800000':
                    $colorIdxFg = 0x10;

                    break;
                case '008000':
                    $colorIdxFg = 0x11;

                    break;
                case '000080':
                    $colorIdxFg = 0x12;

                    break;
                case '808000':
                    $colorIdxFg = 0x13;

                    break;
                case '800080':
                    $colorIdxFg = 0x14;

                    break;
                case '008080':
                    $colorIdxFg = 0x15;

                    break;
                case 'C0C0C0':
                    $colorIdxFg = 0x16;

                    break;
                case '808080':
                    $colorIdxFg = 0x17;

                    break;
                case '9999FF':
                    $colorIdxFg = 0x18;

                    break;
                case '993366':
                    $colorIdxFg = 0x19;

                    break;
                case 'FFFFCC':
                    $colorIdxFg = 0x1A;

                    break;
                case 'CCFFFF':
                    $colorIdxFg = 0x1B;

                    break;
                case '660066':
                    $colorIdxFg = 0x1C;

                    break;
                case 'FF8080':
                    $colorIdxFg = 0x1D;

                    break;
                case '0066CC':
                    $colorIdxFg = 0x1E;

                    break;
                case 'CCCCFF':
                    $colorIdxFg = 0x1F;

                    break;
                case '000080':
                    $colorIdxFg = 0x20;

                    break;
                case 'FF00FF':
                    $colorIdxFg = 0x21;

                    break;
                case 'FFFF00':
                    $colorIdxFg = 0x22;

                    break;
                case '00FFFF':
                    $colorIdxFg = 0x23;

                    break;
                case '800080':
                    $colorIdxFg = 0x24;

                    break;
                case '800000':
                    $colorIdxFg = 0x25;

                    break;
                case '008080':
                    $colorIdxFg = 0x26;

                    break;
                case '0000FF':
                    $colorIdxFg = 0x27;

                    break;
                case '00CCFF':
                    $colorIdxFg = 0x28;

                    break;
                case 'CCFFFF':
                    $colorIdxFg = 0x29;

                    break;
                case 'CCFFCC':
                    $colorIdxFg = 0x2A;

                    break;
                case 'FFFF99':
                    $colorIdxFg = 0x2B;

                    break;
                case '99CCFF':
                    $colorIdxFg = 0x2C;

                    break;
                case 'FF99CC':
                    $colorIdxFg = 0x2D;

                    break;
                case 'CC99FF':
                    $colorIdxFg = 0x2E;

                    break;
                case 'FFCC99':
                    $colorIdxFg = 0x2F;

                    break;
                case '3366FF':
                    $colorIdxFg = 0x30;

                    break;
                case '33CCCC':
                    $colorIdxFg = 0x31;

                    break;
                case '99CC00':
                    $colorIdxFg = 0x32;

                    break;
                case 'FFCC00':
                    $colorIdxFg = 0x33;

                    break;
                case 'FF9900':
                    $colorIdxFg = 0x34;

                    break;
                case 'FF6600':
                    $colorIdxFg = 0x35;

                    break;
                case '666699':
                    $colorIdxFg = 0x36;

                    break;
                case '969696':
                    $colorIdxFg = 0x37;

                    break;
                case '003366':
                    $colorIdxFg = 0x38;

                    break;
                case '339966':
                    $colorIdxFg = 0x39;

                    break;
                case '003300':
                    $colorIdxFg = 0x3A;

                    break;
                case '333300':
                    $colorIdxFg = 0x3B;

                    break;
                case '993300':
                    $colorIdxFg = 0x3C;

                    break;
                case '993366':
                    $colorIdxFg = 0x3D;

                    break;
                case '333399':
                    $colorIdxFg = 0x3E;

                    break;
                case '333333':
                    $colorIdxFg = 0x3F;

                    break;
                default:
                    $colorIdxFg = 0x40;

                    break;
            }
            $dataBlockFill = pack('v', $blockFillPatternStyle);
            $dataBlockFill .= pack('v', $colorIdxFg | ($colorIdxBg << 7));
        }
        if ($bFormatProt == 1) {
            $dataBlockProtection = 0;
            if ($conditional->getStyle()->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED) {
                $dataBlockProtection = 1;
            }
            if ($conditional->getStyle()->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED) {
                $dataBlockProtection = 1 << 1;
            }
        }

        $data = pack('CCvvVv', $type, $operatorType, $szValue1, $szValue2, $flags, 0x0000);
        if ($bFormatFont == 1) { 
            $data .= $dataBlockFont;
        }
        if ($bFormatAlign == 1) {
            $data .= $dataBlockAlign;
        }
        if ($bFormatBorder == 1) {
            $data .= $dataBlockBorder;
        }
        if ($bFormatFill == 1) { 
            $data .= $dataBlockFill;
        }
        if ($bFormatProt == 1) {
            $data .= $dataBlockProtection;
        }
        if ($operand1 !== null) {
            $data .= $operand1;
        }
        if ($operand2 !== null) {
            $data .= $operand2;
        }
        $header = pack('vv', $record, strlen($data));
        $this->append($header . $data);
    }

    
    private function writeCFHeader(): void
    {
        $record = 0x01B0; 
        $length = 0x0016; 

        $numColumnMin = null;
        $numColumnMax = null;
        $numRowMin = null;
        $numRowMax = null;
        $arrConditional = [];
        foreach ($this->phpSheet->getConditionalStylesCollection() as $cellCoordinate => $conditionalStyles) {
            foreach ($conditionalStyles as $conditional) {
                if (
                    $conditional->getConditionType() == Conditional::CONDITION_EXPRESSION
                    || $conditional->getConditionType() == Conditional::CONDITION_CELLIS
                ) {
                    if (!in_array($conditional->getHashCode(), $arrConditional)) {
                        $arrConditional[] = $conditional->getHashCode();
                    }
                    
                    $arrCoord = Coordinate::coordinateFromString($cellCoordinate);
                    if (!is_numeric($arrCoord[0])) {
                        $arrCoord[0] = Coordinate::columnIndexFromString($arrCoord[0]);
                    }
                    if ($numColumnMin === null || ($numColumnMin > $arrCoord[0])) {
                        $numColumnMin = $arrCoord[0];
                    }
                    if ($numColumnMax === null || ($numColumnMax < $arrCoord[0])) {
                        $numColumnMax = $arrCoord[0];
                    }
                    if ($numRowMin === null || ($numRowMin > $arrCoord[1])) {
                        $numRowMin = $arrCoord[1];
                    }
                    if ($numRowMax === null || ($numRowMax < $arrCoord[1])) {
                        $numRowMax = $arrCoord[1];
                    }
                }
            }
        }
        $needRedraw = 1;
        $cellRange = pack('vvvv', $numRowMin - 1, $numRowMax - 1, $numColumnMin - 1, $numColumnMax - 1);

        $header = pack('vv', $record, $length);
        $data = pack('vv', count($arrConditional), $needRedraw);
        $data .= $cellRange;
        $data .= pack('v', 0x0001);
        $data .= $cellRange;
        $this->append($header . $data);
    }
}
