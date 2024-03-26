<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReferenceHelper
{
    
    
    const REFHELPER_REGEXP_CELLREF = '((\w*|\'[^!]*\')!)?(?<![:a-z\$])(\$?[a-z]{1,3}\$?\d+)(?=[^:!\d\'])';
    const REFHELPER_REGEXP_CELLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}\$?\d+):(\$?[a-z]{1,3}\$?\d+)';
    const REFHELPER_REGEXP_ROWRANGE = '((\w*|\'[^!]*\')!)?(\$?\d+):(\$?\d+)';
    const REFHELPER_REGEXP_COLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}):(\$?[a-z]{1,3})';

    
    private static $instance;

    
    public static function getInstance()
    {
        if (!isset(self::$instance) || (self::$instance === null)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    
    protected function __construct()
    {
    }

    
    public static function columnSort($a, $b)
    {
        return strcasecmp(strlen($a) . $a, strlen($b) . $b);
    }

    
    public static function columnReverseSort($a, $b)
    {
        return -strcasecmp(strlen($a) . $a, strlen($b) . $b);
    }

    
    public static function cellSort($a, $b)
    {
        [$ac, $ar] = sscanf($a, '%[A-Z]%d');
        [$bc, $br] = sscanf($b, '%[A-Z]%d');

        if ($ar === $br) {
            return strcasecmp(strlen($ac) . $ac, strlen($bc) . $bc);
        }

        return ($ar < $br) ? -1 : 1;
    }

    
    public static function cellReverseSort($a, $b)
    {
        [$ac, $ar] = sscanf($a, '%[A-Z]%d');
        [$bc, $br] = sscanf($b, '%[A-Z]%d');

        if ($ar === $br) {
            return -strcasecmp(strlen($ac) . $ac, strlen($bc) . $bc);
        }

        return ($ar < $br) ? 1 : -1;
    }

    
    private static function cellAddressInDeleteRange($cellAddress, $beforeRow, $pNumRows, $beforeColumnIndex, $pNumCols)
    {
        [$cellColumn, $cellRow] = Coordinate::coordinateFromString($cellAddress);
        $cellColumnIndex = Coordinate::columnIndexFromString($cellColumn);
        
        if (
            $pNumRows < 0 &&
            ($cellRow >= ($beforeRow + $pNumRows)) &&
            ($cellRow < $beforeRow)
        ) {
            return true;
        } elseif (
            $pNumCols < 0 &&
            ($cellColumnIndex >= ($beforeColumnIndex + $pNumCols)) &&
            ($cellColumnIndex < $beforeColumnIndex)
        ) {
            return true;
        }

        return false;
    }

    
    protected function adjustPageBreaks(Worksheet $pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aBreaks = $pSheet->getBreaks();
        ($pNumCols > 0 || $pNumRows > 0) ?
            uksort($aBreaks, ['self', 'cellReverseSort']) : uksort($aBreaks, ['self', 'cellSort']);

        foreach ($aBreaks as $key => $value) {
            if (self::cellAddressInDeleteRange($key, $beforeRow, $pNumRows, $beforeColumnIndex, $pNumCols)) {
                
                
                $pSheet->setBreak($key, Worksheet::BREAK_NONE);
            } else {
                
                
                $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
                if ($key != $newReference) {
                    $pSheet->setBreak($newReference, $value)
                        ->setBreak($key, Worksheet::BREAK_NONE);
                }
            }
        }
    }

    
    protected function adjustComments($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aComments = $pSheet->getComments();
        $aNewComments = []; 

        foreach ($aComments as $key => &$value) {
            
            if (!self::cellAddressInDeleteRange($key, $beforeRow, $pNumRows, $beforeColumnIndex, $pNumCols)) {
                
                $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
                $aNewComments[$newReference] = $value;
            }
        }
        
        $pSheet->setComments($aNewComments);
    }

    
    protected function adjustHyperlinks($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aHyperlinkCollection = $pSheet->getHyperlinkCollection();
        ($pNumCols > 0 || $pNumRows > 0) ?
            uksort($aHyperlinkCollection, ['self', 'cellReverseSort']) : uksort($aHyperlinkCollection, ['self', 'cellSort']);

        foreach ($aHyperlinkCollection as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->setHyperlink($newReference, $value);
                $pSheet->setHyperlink($key, null);
            }
        }
    }

    
    protected function adjustDataValidations($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aDataValidationCollection = $pSheet->getDataValidationCollection();
        ($pNumCols > 0 || $pNumRows > 0) ?
            uksort($aDataValidationCollection, ['self', 'cellReverseSort']) : uksort($aDataValidationCollection, ['self', 'cellSort']);

        foreach ($aDataValidationCollection as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->setDataValidation($newReference, $value);
                $pSheet->setDataValidation($key, null);
            }
        }
    }

    
    protected function adjustMergeCells($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aMergeCells = $pSheet->getMergeCells();
        $aNewMergeCells = []; 
        foreach ($aMergeCells as $key => &$value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            $aNewMergeCells[$newReference] = $newReference;
        }
        $pSheet->setMergeCells($aNewMergeCells); 
    }

    
    protected function adjustProtectedCells($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aProtectedCells = $pSheet->getProtectedCells();
        ($pNumCols > 0 || $pNumRows > 0) ?
            uksort($aProtectedCells, ['self', 'cellReverseSort']) : uksort($aProtectedCells, ['self', 'cellSort']);
        foreach ($aProtectedCells as $key => $value) {
            $newReference = $this->updateCellReference($key, $pBefore, $pNumCols, $pNumRows);
            if ($key != $newReference) {
                $pSheet->protectCells($newReference, $value, true);
                $pSheet->unprotectCells($key);
            }
        }
    }

    
    protected function adjustColumnDimensions($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aColumnDimensions = array_reverse($pSheet->getColumnDimensions(), true);
        if (!empty($aColumnDimensions)) {
            foreach ($aColumnDimensions as $objColumnDimension) {
                $newReference = $this->updateCellReference($objColumnDimension->getColumnIndex() . '1', $pBefore, $pNumCols, $pNumRows);
                [$newReference] = Coordinate::coordinateFromString($newReference);
                if ($objColumnDimension->getColumnIndex() != $newReference) {
                    $objColumnDimension->setColumnIndex($newReference);
                }
            }
            $pSheet->refreshColumnDimensions();
        }
    }

    
    protected function adjustRowDimensions($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows): void
    {
        $aRowDimensions = array_reverse($pSheet->getRowDimensions(), true);
        if (!empty($aRowDimensions)) {
            foreach ($aRowDimensions as $objRowDimension) {
                $newReference = $this->updateCellReference('A' . $objRowDimension->getRowIndex(), $pBefore, $pNumCols, $pNumRows);
                [, $newReference] = Coordinate::coordinateFromString($newReference);
                if ($objRowDimension->getRowIndex() != $newReference) {
                    $objRowDimension->setRowIndex($newReference);
                }
            }
            $pSheet->refreshRowDimensions();

            $copyDimension = $pSheet->getRowDimension($beforeRow - 1);
            for ($i = $beforeRow; $i <= $beforeRow - 1 + $pNumRows; ++$i) {
                $newDimension = $pSheet->getRowDimension($i);
                $newDimension->setRowHeight($copyDimension->getRowHeight());
                $newDimension->setVisible($copyDimension->getVisible());
                $newDimension->setOutlineLevel($copyDimension->getOutlineLevel());
                $newDimension->setCollapsed($copyDimension->getCollapsed());
            }
        }
    }

    
    public function insertNewBefore($pBefore, $pNumCols, $pNumRows, Worksheet $pSheet): void
    {
        $remove = ($pNumCols < 0 || $pNumRows < 0);
        $allCoordinates = $pSheet->getCoordinates();

        
        [$beforeColumn, $beforeRow] = Coordinate::coordinateFromString($pBefore);
        $beforeColumnIndex = Coordinate::columnIndexFromString($beforeColumn);

        
        $highestColumn = $pSheet->getHighestColumn();
        $highestRow = $pSheet->getHighestRow();

        
        if ($pNumCols < 0 && $beforeColumnIndex - 2 + $pNumCols > 0) {
            for ($i = 1; $i <= $highestRow - 1; ++$i) {
                for ($j = $beforeColumnIndex - 1 + $pNumCols; $j <= $beforeColumnIndex - 2; ++$j) {
                    $coordinate = Coordinate::stringFromColumnIndex($j + 1) . $i;
                    $pSheet->removeConditionalStyles($coordinate);
                    if ($pSheet->cellExists($coordinate)) {
                        $pSheet->getCell($coordinate)->setValueExplicit('', DataType::TYPE_NULL);
                        $pSheet->getCell($coordinate)->setXfIndex(0);
                    }
                }
            }
        }

        
        if ($pNumRows < 0 && $beforeRow - 1 + $pNumRows > 0) {
            for ($i = $beforeColumnIndex - 1; $i <= Coordinate::columnIndexFromString($highestColumn) - 1; ++$i) {
                for ($j = $beforeRow + $pNumRows; $j <= $beforeRow - 1; ++$j) {
                    $coordinate = Coordinate::stringFromColumnIndex($i + 1) . $j;
                    $pSheet->removeConditionalStyles($coordinate);
                    if ($pSheet->cellExists($coordinate)) {
                        $pSheet->getCell($coordinate)->setValueExplicit('', DataType::TYPE_NULL);
                        $pSheet->getCell($coordinate)->setXfIndex(0);
                    }
                }
            }
        }

        
        if ($remove) {
            
            $allCoordinates = array_reverse($allCoordinates);
        }
        while ($coordinate = array_pop($allCoordinates)) {
            $cell = $pSheet->getCell($coordinate);
            $cellIndex = Coordinate::columnIndexFromString($cell->getColumn());

            if ($cellIndex - 1 + $pNumCols < 0) {
                continue;
            }

            
            $newCoordinate = Coordinate::stringFromColumnIndex($cellIndex + $pNumCols) . ($cell->getRow() + $pNumRows);

            
            if (($cellIndex >= $beforeColumnIndex) && ($cell->getRow() >= $beforeRow)) {
                
                $pSheet->getCell($newCoordinate)->setXfIndex($cell->getXfIndex());

                
                if ($cell->getDataType() == DataType::TYPE_FORMULA) {
                    
                    $pSheet->getCell($newCoordinate)
                        ->setValue($this->updateFormulaReferences($cell->getValue(), $pBefore, $pNumCols, $pNumRows, $pSheet->getTitle()));
                } else {
                    
                    $pSheet->getCell($newCoordinate)->setValue($cell->getValue());
                }

                
                $pSheet->getCellCollection()->delete($coordinate);
            } else {
                
                if ($cell->getDataType() == DataType::TYPE_FORMULA) {
                    
                    $cell->setValue($this->updateFormulaReferences($cell->getValue(), $pBefore, $pNumCols, $pNumRows, $pSheet->getTitle()));
                }
            }
        }

        
        $highestColumn = $pSheet->getHighestColumn();
        $highestRow = $pSheet->getHighestRow();

        if ($pNumCols > 0 && $beforeColumnIndex - 2 > 0) {
            for ($i = $beforeRow; $i <= $highestRow - 1; ++$i) {
                
                $coordinate = Coordinate::stringFromColumnIndex($beforeColumnIndex - 1) . $i;
                if ($pSheet->cellExists($coordinate)) {
                    $xfIndex = $pSheet->getCell($coordinate)->getXfIndex();
                    $conditionalStyles = $pSheet->conditionalStylesExists($coordinate) ?
                        $pSheet->getConditionalStyles($coordinate) : false;
                    for ($j = $beforeColumnIndex; $j <= $beforeColumnIndex - 1 + $pNumCols; ++$j) {
                        $pSheet->getCellByColumnAndRow($j, $i)->setXfIndex($xfIndex);
                        if ($conditionalStyles) {
                            $cloned = [];
                            foreach ($conditionalStyles as $conditionalStyle) {
                                $cloned[] = clone $conditionalStyle;
                            }
                            $pSheet->setConditionalStyles(Coordinate::stringFromColumnIndex($j) . $i, $cloned);
                        }
                    }
                }
            }
        }

        if ($pNumRows > 0 && $beforeRow - 1 > 0) {
            for ($i = $beforeColumnIndex; $i <= Coordinate::columnIndexFromString($highestColumn); ++$i) {
                
                $coordinate = Coordinate::stringFromColumnIndex($i) . ($beforeRow - 1);
                if ($pSheet->cellExists($coordinate)) {
                    $xfIndex = $pSheet->getCell($coordinate)->getXfIndex();
                    $conditionalStyles = $pSheet->conditionalStylesExists($coordinate) ?
                        $pSheet->getConditionalStyles($coordinate) : false;
                    for ($j = $beforeRow; $j <= $beforeRow - 1 + $pNumRows; ++$j) {
                        $pSheet->getCell(Coordinate::stringFromColumnIndex($i) . $j)->setXfIndex($xfIndex);
                        if ($conditionalStyles) {
                            $cloned = [];
                            foreach ($conditionalStyles as $conditionalStyle) {
                                $cloned[] = clone $conditionalStyle;
                            }
                            $pSheet->setConditionalStyles(Coordinate::stringFromColumnIndex($i) . $j, $cloned);
                        }
                    }
                }
            }
        }

        
        $this->adjustColumnDimensions($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustRowDimensions($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustPageBreaks($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustComments($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustHyperlinks($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustDataValidations($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustMergeCells($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $this->adjustProtectedCells($pSheet, $pBefore, $beforeColumnIndex, $pNumCols, $beforeRow, $pNumRows);

        
        $autoFilter = $pSheet->getAutoFilter();
        $autoFilterRange = $autoFilter->getRange();
        if (!empty($autoFilterRange)) {
            if ($pNumCols != 0) {
                $autoFilterColumns = $autoFilter->getColumns();
                if (count($autoFilterColumns) > 0) {
                    $column = '';
                    $row = 0;
                    sscanf($pBefore, '%[A-Z]%d', $column, $row);
                    $columnIndex = Coordinate::columnIndexFromString($column);
                    [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($autoFilterRange);
                    if ($columnIndex <= $rangeEnd[0]) {
                        if ($pNumCols < 0) {
                            
                            
                            $deleteColumn = $columnIndex + $pNumCols - 1;
                            $deleteCount = abs($pNumCols);
                            for ($i = 1; $i <= $deleteCount; ++$i) {
                                if (isset($autoFilterColumns[Coordinate::stringFromColumnIndex($deleteColumn + 1)])) {
                                    $autoFilter->clearColumn(Coordinate::stringFromColumnIndex($deleteColumn + 1));
                                }
                                ++$deleteColumn;
                            }
                        }
                        $startCol = ($columnIndex > $rangeStart[0]) ? $columnIndex : $rangeStart[0];

                        
                        if ($pNumCols > 0) {
                            $startColRef = $startCol;
                            $endColRef = $rangeEnd[0];
                            $toColRef = $rangeEnd[0] + $pNumCols;

                            do {
                                $autoFilter->shiftColumn(Coordinate::stringFromColumnIndex($endColRef), Coordinate::stringFromColumnIndex($toColRef));
                                --$endColRef;
                                --$toColRef;
                            } while ($startColRef <= $endColRef);
                        } else {
                            
                            $startColID = Coordinate::stringFromColumnIndex($startCol);
                            $toColID = Coordinate::stringFromColumnIndex($startCol + $pNumCols);
                            $endColID = Coordinate::stringFromColumnIndex($rangeEnd[0] + 1);
                            do {
                                $autoFilter->shiftColumn($startColID, $toColID);
                                ++$startColID;
                                ++$toColID;
                            } while ($startColID != $endColID);
                        }
                    }
                }
            }
            $pSheet->setAutoFilter($this->updateCellReference($autoFilterRange, $pBefore, $pNumCols, $pNumRows));
        }

        
        if ($pSheet->getFreezePane()) {
            $splitCell = $pSheet->getFreezePane();
            $topLeftCell = $pSheet->getTopLeftCell();

            $splitCell = $this->updateCellReference($splitCell, $pBefore, $pNumCols, $pNumRows);
            $topLeftCell = $this->updateCellReference($topLeftCell, $pBefore, $pNumCols, $pNumRows);

            $pSheet->freezePane($splitCell, $topLeftCell);
        }

        
        if ($pSheet->getPageSetup()->isPrintAreaSet()) {
            $pSheet->getPageSetup()->setPrintArea($this->updateCellReference($pSheet->getPageSetup()->getPrintArea(), $pBefore, $pNumCols, $pNumRows));
        }

        
        $aDrawings = $pSheet->getDrawingCollection();
        foreach ($aDrawings as $objDrawing) {
            $newReference = $this->updateCellReference($objDrawing->getCoordinates(), $pBefore, $pNumCols, $pNumRows);
            if ($objDrawing->getCoordinates() != $newReference) {
                $objDrawing->setCoordinates($newReference);
            }
        }

        
        if (count($pSheet->getParent()->getDefinedNames()) > 0) {
            foreach ($pSheet->getParent()->getDefinedNames() as $definedName) {
                if ($definedName->getWorksheet()->getHashCode() === $pSheet->getHashCode()) {
                    $definedName->setValue($this->updateCellReference($definedName->getValue(), $pBefore, $pNumCols, $pNumRows));
                }
            }
        }

        
        $pSheet->garbageCollect();
    }

    
    public function updateFormulaReferences($pFormula = '', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0, $sheetName = '')
    {
        
        $formulaBlocks = explode('"', $pFormula);
        $i = false;
        foreach ($formulaBlocks as &$formulaBlock) {
            
            if ($i = !$i) {
                $adjustCount = 0;
                $newCellTokens = $cellTokens = [];
                
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_ROWRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference('$A' . $match[3], $pBefore, $pNumCols, $pNumRows), 2);
                        $modified4 = substr($this->updateCellReference('$A' . $match[4], $pBefore, $pNumCols, $pNumRows), 2);

                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                
                                $column = 100000;
                                $row = 10000000 + trim($match[3], '$');
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<!\d\$\!)' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_COLRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference($match[3] . '$1', $pBefore, $pNumCols, $pNumRows), 0, -2);
                        $modified4 = substr($this->updateCellReference($match[4] . '$1', $pBefore, $pNumCols, $pNumRows), 0, -2);

                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                
                                $column = Coordinate::columnIndexFromString(trim($match[3], '$')) + 100000;
                                $row = 10000000;
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z\$\!])' . preg_quote($fromString, '/') . '(?![A-Z])/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLRANGE . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = $this->updateCellReference($match[3], $pBefore, $pNumCols, $pNumRows);
                        $modified4 = $this->updateCellReference($match[4], $pBefore, $pNumCols, $pNumRows);

                        if ($match[3] . $match[4] !== $modified3 . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                [$column, $row] = Coordinate::coordinateFromString($match[3]);
                                
                                $column = Coordinate::columnIndexFromString(trim($column, '$')) + 100000;
                                $row = trim($row, '$') + 10000000;
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z]\$\!)' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLREF . '/i', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);

                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3];

                        $modified3 = $this->updateCellReference($match[3], $pBefore, $pNumCols, $pNumRows);
                        if ($match[3] !== $modified3) {
                            if (($match[2] == '') || (trim($match[2], "'") == $sheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3;
                                [$column, $row] = Coordinate::coordinateFromString($match[3]);
                                
                                $column = Coordinate::columnIndexFromString(trim($column, '$')) + 100000;
                                $row = trim($row, '$') + 10000000;
                                $cellIndex = $row . $column;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z\$\!])' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                if ($adjustCount > 0) {
                    if ($pNumCols > 0 || $pNumRows > 0) {
                        krsort($cellTokens);
                        krsort($newCellTokens);
                    } else {
                        ksort($cellTokens);
                        ksort($newCellTokens);
                    }   
                    $formulaBlock = str_replace('\\', '', preg_replace($cellTokens, $newCellTokens, $formulaBlock));
                }
            }
        }
        unset($formulaBlock);

        
        return implode('"', $formulaBlocks);
    }

    
    public function updateFormulaReferencesAnyWorksheet(string $formula = '', int $insertColumns = 0, int $insertRows = 0): string
    {
        $formula = $this->updateCellReferencesAllWorksheets($formula, $insertColumns, $insertRows);

        if ($insertColumns !== 0) {
            $formula = $this->updateColumnRangesAllWorksheets($formula, $insertColumns);
        }

        if ($insertRows !== 0) {
            $formula = $this->updateRowRangesAllWorksheets($formula, $insertRows);
        }

        return $formula;
    }

    private function updateCellReferencesAllWorksheets(string $formula, int $insertColumns, int $insertRows): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $columnLengths = array_map('strlen', array_column($splitRanges[6], 0));
        $rowLengths = array_map('strlen', array_column($splitRanges[7], 0));
        $columnOffsets = array_column($splitRanges[6], 1);
        $rowOffsets = array_column($splitRanges[7], 1);

        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        while ($splitCount > 0) {
            --$splitCount;
            $columnLength = $columnLengths[$splitCount];
            $rowLength = $rowLengths[$splitCount];
            $columnOffset = $columnOffsets[$splitCount];
            $rowOffset = $rowOffsets[$splitCount];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            if (!empty($column) && $column[0] !== '$') {
                $column = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($column) + $insertColumns);
                $formula = substr($formula, 0, $columnOffset) . $column . substr($formula, $columnOffset + $columnLength);
            }
            if (!empty($row) && $row[0] !== '$') {
                $row += $insertRows;
                $formula = substr($formula, 0, $rowOffset) . $row . substr($formula, $rowOffset + $rowLength);
            }
        }

        return $formula;
    }

    private function updateColumnRangesAllWorksheets(string $formula, int $insertColumns): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_COLUMNRANGE_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $fromColumnLengths = array_map('strlen', array_column($splitRanges[1], 0));
        $fromColumnOffsets = array_column($splitRanges[1], 1);
        $toColumnLengths = array_map('strlen', array_column($splitRanges[2], 0));
        $toColumnOffsets = array_column($splitRanges[2], 1);

        $fromColumns = $splitRanges[1];
        $toColumns = $splitRanges[2];

        while ($splitCount > 0) {
            --$splitCount;
            $fromColumnLength = $fromColumnLengths[$splitCount];
            $toColumnLength = $toColumnLengths[$splitCount];
            $fromColumnOffset = $fromColumnOffsets[$splitCount];
            $toColumnOffset = $toColumnOffsets[$splitCount];
            $fromColumn = $fromColumns[$splitCount][0];
            $toColumn = $toColumns[$splitCount][0];

            if (!empty($fromColumn) && $fromColumn[0] !== '$') {
                $fromColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($fromColumn) + $insertColumns);
                $formula = substr($formula, 0, $fromColumnOffset) . $fromColumn . substr($formula, $fromColumnOffset + $fromColumnLength);
            }
            if (!empty($toColumn) && $toColumn[0] !== '$') {
                $toColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($toColumn) + $insertColumns);
                $formula = substr($formula, 0, $toColumnOffset) . $toColumn . substr($formula, $toColumnOffset + $toColumnLength);
            }
        }

        return $formula;
    }

    private function updateRowRangesAllWorksheets(string $formula, int $insertRows): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_ROWRANGE_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $fromRowLengths = array_map('strlen', array_column($splitRanges[1], 0));
        $fromRowOffsets = array_column($splitRanges[1], 1);
        $toRowLengths = array_map('strlen', array_column($splitRanges[2], 0));
        $toRowOffsets = array_column($splitRanges[2], 1);

        $fromRows = $splitRanges[1];
        $toRows = $splitRanges[2];

        while ($splitCount > 0) {
            --$splitCount;
            $fromRowLength = $fromRowLengths[$splitCount];
            $toRowLength = $toRowLengths[$splitCount];
            $fromRowOffset = $fromRowOffsets[$splitCount];
            $toRowOffset = $toRowOffsets[$splitCount];
            $fromRow = $fromRows[$splitCount][0];
            $toRow = $toRows[$splitCount][0];

            if (!empty($fromRow) && $fromRow[0] !== '$') {
                $fromRow += $insertRows;
                $formula = substr($formula, 0, $fromRowOffset) . $fromRow . substr($formula, $fromRowOffset + $fromRowLength);
            }
            if (!empty($toRow) && $toRow[0] !== '$') {
                $toRow += $insertRows;
                $formula = substr($formula, 0, $toRowOffset) . $toRow . substr($formula, $toRowOffset + $toRowLength);
            }
        }

        return $formula;
    }

    
    public function updateCellReference($pCellRange = 'A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        
        if (strpos($pCellRange, '!') !== false) {
            return $pCellRange;
        
        } elseif (!Coordinate::coordinateIsRange($pCellRange)) {
            
            return $this->updateSingleCellReference($pCellRange, $pBefore, $pNumCols, $pNumRows);
        } elseif (Coordinate::coordinateIsRange($pCellRange)) {
            
            return $this->updateCellRange($pCellRange, $pBefore, $pNumCols, $pNumRows);
        }

        
        return $pCellRange;
    }

    
    public function updateNamedFormulas(Spreadsheet $spreadsheet, $oldName = '', $newName = ''): void
    {
        if ($oldName == '') {
            return;
        }

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            foreach ($sheet->getCoordinates(false) as $coordinate) {
                $cell = $sheet->getCell($coordinate);
                if (($cell !== null) && ($cell->getDataType() == DataType::TYPE_FORMULA)) {
                    $formula = $cell->getValue();
                    if (strpos($formula, $oldName) !== false) {
                        $formula = str_replace("'" . $oldName . "'!", "'" . $newName . "'!", $formula);
                        $formula = str_replace($oldName . '!', $newName . '!', $formula);
                        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);
                    }
                }
            }
        }
    }

    
    private function updateCellRange($pCellRange = 'A1:A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        if (!Coordinate::coordinateIsRange($pCellRange)) {
            throw new Exception('Only cell ranges may be passed to this method.');
        }

        
        $range = Coordinate::splitRange($pCellRange);
        $ic = count($range);
        for ($i = 0; $i < $ic; ++$i) {
            $jc = count($range[$i]);
            for ($j = 0; $j < $jc; ++$j) {
                if (ctype_alpha($range[$i][$j])) {
                    $r = Coordinate::coordinateFromString($this->updateSingleCellReference($range[$i][$j] . '1', $pBefore, $pNumCols, $pNumRows));
                    $range[$i][$j] = $r[0];
                } elseif (ctype_digit($range[$i][$j])) {
                    $r = Coordinate::coordinateFromString($this->updateSingleCellReference('A' . $range[$i][$j], $pBefore, $pNumCols, $pNumRows));
                    $range[$i][$j] = $r[1];
                } else {
                    $range[$i][$j] = $this->updateSingleCellReference($range[$i][$j], $pBefore, $pNumCols, $pNumRows);
                }
            }
        }

        
        return Coordinate::buildRange($range);
    }

    
    private function updateSingleCellReference($pCellReference = 'A1', $pBefore = 'A1', $pNumCols = 0, $pNumRows = 0)
    {
        if (Coordinate::coordinateIsRange($pCellReference)) {
            throw new Exception('Only single cell references may be passed to this method.');
        }

        
        [$beforeColumn, $beforeRow] = Coordinate::coordinateFromString($pBefore);

        
        [$newColumn, $newRow] = Coordinate::coordinateFromString($pCellReference);

        
        $updateColumn = (($newColumn[0] != '$') && ($beforeColumn[0] != '$') && (Coordinate::columnIndexFromString($newColumn) >= Coordinate::columnIndexFromString($beforeColumn)));
        $updateRow = (($newRow[0] != '$') && ($beforeRow[0] != '$') && $newRow >= $beforeRow);

        
        if ($updateColumn) {
            $newColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($newColumn) + $pNumCols);
        }

        
        if ($updateRow) {
            $newRow = $newRow + $pNumRows;
        }

        
        return $newColumn . $newRow;
    }

    
    final public function __clone()
    {
        throw new Exception('Cloning a Singleton is not allowed!');
    }
}
