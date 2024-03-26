<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Style extends Supervisor
{
    
    protected $font;

    
    protected $fill;

    
    protected $borders;

    
    protected $alignment;

    
    protected $numberFormat;

    
    protected $protection;

    
    protected $index;

    
    protected $quotePrefix = false;

    
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        parent::__construct($isSupervisor);

        
        $this->font = new Font($isSupervisor, $isConditional);
        $this->fill = new Fill($isSupervisor, $isConditional);
        $this->borders = new Borders($isSupervisor, $isConditional);
        $this->alignment = new Alignment($isSupervisor, $isConditional);
        $this->numberFormat = new NumberFormat($isSupervisor, $isConditional);
        $this->protection = new Protection($isSupervisor, $isConditional);

        
        if ($isSupervisor) {
            $this->font->bindParent($this);
            $this->fill->bindParent($this);
            $this->borders->bindParent($this);
            $this->alignment->bindParent($this);
            $this->numberFormat->bindParent($this);
            $this->protection->bindParent($this);
        }
    }

    
    public function getSharedComponent()
    {
        $activeSheet = $this->getActiveSheet();
        $selectedCell = $this->getActiveCell(); 

        if ($activeSheet->cellExists($selectedCell)) {
            $xfIndex = $activeSheet->getCell($selectedCell)->getXfIndex();
        } else {
            $xfIndex = 0;
        }

        return $this->parent->getCellXfByIndex($xfIndex);
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function getStyleArray($array)
    {
        return ['quotePrefix' => $array];
    }

    
    public function applyFromArray(array $pStyles, $pAdvanced = true)
    {
        if ($this->isSupervisor) {
            $pRange = $this->getSelectedCells();

            
            $pRange = strtoupper($pRange);

            
            if (strpos($pRange, ':') === false) {
                $rangeA = $pRange;
                $rangeB = $pRange;
            } else {
                [$rangeA, $rangeB] = explode(':', $pRange);
            }

            
            $rangeStart = Coordinate::coordinateFromString($rangeA);
            $rangeEnd = Coordinate::coordinateFromString($rangeB);

            
            $rangeStart0 = $rangeStart[0];
            $rangeEnd0 = $rangeEnd[0];
            $rangeStart[0] = Coordinate::columnIndexFromString($rangeStart[0]);
            $rangeEnd[0] = Coordinate::columnIndexFromString($rangeEnd[0]);

            
            if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
                $tmp = $rangeStart;
                $rangeStart = $rangeEnd;
                $rangeEnd = $tmp;
            }

            
            if ($pAdvanced && isset($pStyles['borders'])) {
                
                
                if (isset($pStyles['borders']['allBorders'])) {
                    foreach (['outline', 'inside'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['allBorders'];
                        }
                    }
                    unset($pStyles['borders']['allBorders']); 
                }
                
                
                if (isset($pStyles['borders']['outline'])) {
                    foreach (['top', 'right', 'bottom', 'left'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['outline'];
                        }
                    }
                    unset($pStyles['borders']['outline']); 
                }
                
                
                if (isset($pStyles['borders']['inside'])) {
                    foreach (['vertical', 'horizontal'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['inside'];
                        }
                    }
                    unset($pStyles['borders']['inside']); 
                }
                
                $xMax = min($rangeEnd[0] - $rangeStart[0] + 1, 3);
                $yMax = min($rangeEnd[1] - $rangeStart[1] + 1, 3);

                
                for ($x = 1; $x <= $xMax; ++$x) {
                    
                    $colStart = ($x == 3) ?
                        Coordinate::stringFromColumnIndex($rangeEnd[0])
                            : Coordinate::stringFromColumnIndex($rangeStart[0] + $x - 1);
                    
                    $colEnd = ($x == 1) ?
                        Coordinate::stringFromColumnIndex($rangeStart[0])
                            : Coordinate::stringFromColumnIndex($rangeEnd[0] - $xMax + $x);

                    for ($y = 1; $y <= $yMax; ++$y) {
                        
                        $edges = [];
                        if ($x == 1) {
                            
                            $edges[] = 'left';
                        }
                        if ($x == $xMax) {
                            
                            $edges[] = 'right';
                        }
                        if ($y == 1) {
                            
                            $edges[] = 'top';
                        }
                        if ($y == $yMax) {
                            
                            $edges[] = 'bottom';
                        }

                        
                        $rowStart = ($y == 3) ?
                            $rangeEnd[1] : $rangeStart[1] + $y - 1;

                        
                        $rowEnd = ($y == 1) ?
                            $rangeStart[1] : $rangeEnd[1] - $yMax + $y;

                        
                        $range = $colStart . $rowStart . ':' . $colEnd . $rowEnd;

                        
                        $regionStyles = $pStyles;
                        unset($regionStyles['borders']['inside']);

                        
                        $innerEdges = array_diff(['top', 'right', 'bottom', 'left'], $edges);

                        
                        foreach ($innerEdges as $innerEdge) {
                            switch ($innerEdge) {
                                case 'top':
                                case 'bottom':
                                    
                                    if (isset($pStyles['borders']['horizontal'])) {
                                        $regionStyles['borders'][$innerEdge] = $pStyles['borders']['horizontal'];
                                    } else {
                                        unset($regionStyles['borders'][$innerEdge]);
                                    }

                                    break;
                                case 'left':
                                case 'right':
                                    
                                    if (isset($pStyles['borders']['vertical'])) {
                                        $regionStyles['borders'][$innerEdge] = $pStyles['borders']['vertical'];
                                    } else {
                                        unset($regionStyles['borders'][$innerEdge]);
                                    }

                                    break;
                            }
                        }

                        
                        $this->getActiveSheet()->getStyle($range)->applyFromArray($regionStyles, false);
                    }
                }

                
                $this->getActiveSheet()->getStyle($pRange);

                return $this;
            }

            
            
            if (preg_match('/^[A-Z]+1:[A-Z]+1048576$/', $pRange)) {
                $selectionType = 'COLUMN';
            } elseif (preg_match('/^A\d+:XFD\d+$/', $pRange)) {
                $selectionType = 'ROW';
            } else {
                $selectionType = 'CELL';
            }

            
            switch ($selectionType) {
                case 'COLUMN':
                    $oldXfIndexes = [];
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        $oldXfIndexes[$this->getActiveSheet()->getColumnDimensionByColumn($col)->getXfIndex()] = true;
                    }
                    foreach ($this->getActiveSheet()->getColumnIterator($rangeStart0, $rangeEnd0) as $columnIterator) {
                        $cellIterator = $columnIterator->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        foreach ($cellIterator as $columnCell) {
                            $columnCell->getStyle()->applyFromArray($pStyles);
                        }
                    }

                    break;
                case 'ROW':
                    $oldXfIndexes = [];
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        if ($this->getActiveSheet()->getRowDimension($row)->getXfIndex() == null) {
                            $oldXfIndexes[0] = true; 
                        } else {
                            $oldXfIndexes[$this->getActiveSheet()->getRowDimension($row)->getXfIndex()] = true;
                        }
                    }
                    foreach ($this->getActiveSheet()->getRowIterator((int) $rangeStart[1], (int) $rangeEnd[1]) as $rowIterator) {
                        $cellIterator = $rowIterator->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        foreach ($cellIterator as $rowCell) {
                            $rowCell->getStyle()->applyFromArray($pStyles);
                        }
                    }

                    break;
                case 'CELL':
                    $oldXfIndexes = [];
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                            $oldXfIndexes[$this->getActiveSheet()->getCellByColumnAndRow($col, $row)->getXfIndex()] = true;
                        }
                    }

                    break;
            }

            
            $workbook = $this->getActiveSheet()->getParent();
            foreach ($oldXfIndexes as $oldXfIndex => $dummy) {
                $style = $workbook->getCellXfByIndex($oldXfIndex);
                $newStyle = clone $style;
                $newStyle->applyFromArray($pStyles);

                if ($existingStyle = $workbook->getCellXfByHashCode($newStyle->getHashCode())) {
                    
                    $newXfIndexes[$oldXfIndex] = $existingStyle->getIndex();
                } else {
                    
                    $workbook->addCellXf($newStyle);
                    $newXfIndexes[$oldXfIndex] = $newStyle->getIndex();
                }
            }

            
            switch ($selectionType) {
                case 'COLUMN':
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        $columnDimension = $this->getActiveSheet()->getColumnDimensionByColumn($col);
                        $oldXfIndex = $columnDimension->getXfIndex();
                        $columnDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    break;
                case 'ROW':
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        $rowDimension = $this->getActiveSheet()->getRowDimension($row);
                        $oldXfIndex = $rowDimension->getXfIndex() === null ?
                            0 : $rowDimension->getXfIndex(); 
                        $rowDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    break;
                case 'CELL':
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                            $cell = $this->getActiveSheet()->getCellByColumnAndRow($col, $row);
                            $oldXfIndex = $cell->getXfIndex();
                            $cell->setXfIndex($newXfIndexes[$oldXfIndex]);
                        }
                    }

                    break;
            }
        } else {
            
            if (isset($pStyles['fill'])) {
                $this->getFill()->applyFromArray($pStyles['fill']);
            }
            if (isset($pStyles['font'])) {
                $this->getFont()->applyFromArray($pStyles['font']);
            }
            if (isset($pStyles['borders'])) {
                $this->getBorders()->applyFromArray($pStyles['borders']);
            }
            if (isset($pStyles['alignment'])) {
                $this->getAlignment()->applyFromArray($pStyles['alignment']);
            }
            if (isset($pStyles['numberFormat'])) {
                $this->getNumberFormat()->applyFromArray($pStyles['numberFormat']);
            }
            if (isset($pStyles['protection'])) {
                $this->getProtection()->applyFromArray($pStyles['protection']);
            }
            if (isset($pStyles['quotePrefix'])) {
                $this->quotePrefix = $pStyles['quotePrefix'];
            }
        }

        return $this;
    }

    
    public function getFill()
    {
        return $this->fill;
    }

    
    public function getFont()
    {
        return $this->font;
    }

    
    public function setFont(Font $font)
    {
        $this->font = $font;

        return $this;
    }

    
    public function getBorders()
    {
        return $this->borders;
    }

    
    public function getAlignment()
    {
        return $this->alignment;
    }

    
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }

    
    public function getConditionalStyles()
    {
        return $this->getActiveSheet()->getConditionalStyles($this->getActiveCell());
    }

    
    public function setConditionalStyles(array $pValue)
    {
        $this->getActiveSheet()->setConditionalStyles($this->getSelectedCells(), $pValue);

        return $this;
    }

    
    public function getProtection()
    {
        return $this->protection;
    }

    
    public function getQuotePrefix()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getQuotePrefix();
        }

        return $this->quotePrefix;
    }

    
    public function setQuotePrefix($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = ['quotePrefix' => $pValue];
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->quotePrefix = (bool) $pValue;
        }

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->fill->getHashCode() .
            $this->font->getHashCode() .
            $this->borders->getHashCode() .
            $this->alignment->getHashCode() .
            $this->numberFormat->getHashCode() .
            $this->protection->getHashCode() .
            ($this->quotePrefix ? 't' : 'f') .
            __CLASS__
        );
    }

    
    public function getIndex()
    {
        return $this->index;
    }

    
    public function setIndex($pValue): void
    {
        $this->index = $pValue;
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'alignment', $this->getAlignment());
        $this->exportArray2($exportedArray, 'borders', $this->getBorders());
        $this->exportArray2($exportedArray, 'fill', $this->getFill());
        $this->exportArray2($exportedArray, 'font', $this->getFont());
        $this->exportArray2($exportedArray, 'numberFormat', $this->getNumberFormat());
        $this->exportArray2($exportedArray, 'protection', $this->getProtection());
        $this->exportArray2($exportedArray, 'quotePrefx', $this->getQuotePrefix());

        return $exportedArray;
    }
}
