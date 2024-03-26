<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use ArrayObject;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

class Worksheet implements IComparable
{
    
    const BREAK_NONE = 0;
    const BREAK_ROW = 1;
    const BREAK_COLUMN = 2;

    
    const SHEETSTATE_VISIBLE = 'visible';
    const SHEETSTATE_HIDDEN = 'hidden';
    const SHEETSTATE_VERYHIDDEN = 'veryHidden';

    
    const SHEET_TITLE_MAXIMUM_LENGTH = 31;

    
    private static $invalidCharacters = ['*', ':', '/', '\\', '?', '[', ']'];

    
    private $parent;

    
    private $cellCollection;

    
    private $rowDimensions = [];

    
    private $defaultRowDimension;

    
    private $columnDimensions = [];

    
    private $defaultColumnDimension;

    
    private $drawingCollection;

    
    private $chartCollection = [];

    
    private $title;

    
    private $sheetState;

    
    private $pageSetup;

    
    private $pageMargins;

    
    private $headerFooter;

    
    private $sheetView;

    
    private $protection;

    
    private $styles = [];

    
    private $conditionalStylesCollection = [];

    
    private $cellCollectionIsSorted = false;

    
    private $breaks = [];

    
    private $mergeCells = [];

    
    private $protectedCells = [];

    
    private $autoFilter;

    
    private $freezePane;

    
    private $topLeftCell;

    
    private $showGridlines = true;

    
    private $printGridlines = false;

    
    private $showRowColHeaders = true;

    
    private $showSummaryBelow = true;

    
    private $showSummaryRight = true;

    
    private $comments = [];

    
    private $activeCell = 'A1';

    
    private $selectedCells = 'A1';

    
    private $cachedHighestColumn = 'A';

    
    private $cachedHighestRow = 1;

    
    private $rightToLeft = false;

    
    private $hyperlinkCollection = [];

    
    private $dataValidationCollection = [];

    
    private $tabColor;

    
    private $dirty = true;

    
    private $hash;

    
    private $codeName;

    
    public function __construct(?Spreadsheet $parent = null, $pTitle = 'Worksheet')
    {
        
        $this->parent = $parent;
        $this->setTitle($pTitle, false);
        
        $this->setCodeName($this->getTitle());
        $this->setSheetState(self::SHEETSTATE_VISIBLE);

        $this->cellCollection = CellsFactory::getInstance($this);
        
        $this->pageSetup = new PageSetup();
        
        $this->pageMargins = new PageMargins();
        
        $this->headerFooter = new HeaderFooter();
        
        $this->sheetView = new SheetView();
        
        $this->drawingCollection = new ArrayObject();
        
        $this->chartCollection = new ArrayObject();
        
        $this->protection = new Protection();
        
        $this->defaultRowDimension = new RowDimension(null);
        
        $this->defaultColumnDimension = new ColumnDimension(null);
        $this->autoFilter = new AutoFilter(null, $this);
    }

    
    public function disconnectCells(): void
    {
        if ($this->cellCollection !== null) {
            $this->cellCollection->unsetWorksheetCells();
            $this->cellCollection = null;
        }
        
        $this->parent = null;
    }

    
    public function __destruct()
    {
        Calculation::getInstance($this->parent)->clearCalculationCacheForWorksheet($this->title);

        $this->disconnectCells();
    }

    
    public function getCellCollection()
    {
        return $this->cellCollection;
    }

    
    public static function getInvalidCharacters()
    {
        return self::$invalidCharacters;
    }

    
    private static function checkSheetCodeName($pValue)
    {
        $CharCount = Shared\StringHelper::countCharacters($pValue);
        if ($CharCount == 0) {
            throw new Exception('Sheet code name cannot be empty.');
        }
        
        if (
            (str_replace(self::$invalidCharacters, '', $pValue) !== $pValue) ||
            (Shared\StringHelper::substring($pValue, -1, 1) == '\'') ||
            (Shared\StringHelper::substring($pValue, 0, 1) == '\'')
        ) {
            throw new Exception('Invalid character found in sheet code name');
        }

        
        if ($CharCount > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet code name.');
        }

        return $pValue;
    }

    
    private static function checkSheetTitle($pValue)
    {
        
        if (str_replace(self::$invalidCharacters, '', $pValue) !== $pValue) {
            throw new Exception('Invalid character found in sheet title');
        }

        
        if (Shared\StringHelper::countCharacters($pValue) > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet title.');
        }

        return $pValue;
    }

    
    public function getCoordinates($sorted = true)
    {
        if ($this->cellCollection == null) {
            return [];
        }

        if ($sorted) {
            return $this->cellCollection->getSortedCoordinates();
        }

        return $this->cellCollection->getCoordinates();
    }

    
    public function getRowDimensions()
    {
        return $this->rowDimensions;
    }

    
    public function getDefaultRowDimension()
    {
        return $this->defaultRowDimension;
    }

    
    public function getColumnDimensions()
    {
        return $this->columnDimensions;
    }

    
    public function getDefaultColumnDimension()
    {
        return $this->defaultColumnDimension;
    }

    
    public function getDrawingCollection()
    {
        return $this->drawingCollection;
    }

    
    public function getChartCollection()
    {
        return $this->chartCollection;
    }

    
    public function addChart(Chart $pChart, $iChartIndex = null)
    {
        $pChart->setWorksheet($this);
        if ($iChartIndex === null) {
            $this->chartCollection[] = $pChart;
        } else {
            
            array_splice($this->chartCollection, $iChartIndex, 0, [$pChart]);
        }

        return $pChart;
    }

    
    public function getChartCount()
    {
        return count($this->chartCollection);
    }

    
    public function getChartByIndex($index)
    {
        $chartCount = count($this->chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        if ($index === null) {
            $index = --$chartCount;
        }
        if (!isset($this->chartCollection[$index])) {
            return false;
        }

        return $this->chartCollection[$index];
    }

    
    public function getChartNames()
    {
        $chartNames = [];
        foreach ($this->chartCollection as $chart) {
            $chartNames[] = $chart->getName();
        }

        return $chartNames;
    }

    
    public function getChartByName($chartName)
    {
        $chartCount = count($this->chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        foreach ($this->chartCollection as $index => $chart) {
            if ($chart->getName() == $chartName) {
                return $this->chartCollection[$index];
            }
        }

        return false;
    }

    
    public function refreshColumnDimensions()
    {
        $currentColumnDimensions = $this->getColumnDimensions();
        $newColumnDimensions = [];

        foreach ($currentColumnDimensions as $objColumnDimension) {
            $newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
        }

        $this->columnDimensions = $newColumnDimensions;

        return $this;
    }

    
    public function refreshRowDimensions()
    {
        $currentRowDimensions = $this->getRowDimensions();
        $newRowDimensions = [];

        foreach ($currentRowDimensions as $objRowDimension) {
            $newRowDimensions[$objRowDimension->getRowIndex()] = $objRowDimension;
        }

        $this->rowDimensions = $newRowDimensions;

        return $this;
    }

    
    public function calculateWorksheetDimension()
    {
        
        return 'A1:' . $this->getHighestColumn() . $this->getHighestRow();
    }

    
    public function calculateWorksheetDataDimension()
    {
        
        return 'A1:' . $this->getHighestDataColumn() . $this->getHighestDataRow();
    }

    
    public function calculateColumnWidths()
    {
        
        $autoSizes = [];
        foreach ($this->getColumnDimensions() as $colDimension) {
            if ($colDimension->getAutoSize()) {
                $autoSizes[$colDimension->getColumnIndex()] = -1;
            }
        }

        
        if (!empty($autoSizes)) {
            
            $isMergeCell = [];
            foreach ($this->getMergeCells() as $cells) {
                foreach (Coordinate::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }

            
            foreach ($this->getCoordinates(false) as $coordinate) {
                $cell = $this->getCell($coordinate, false);
                if ($cell !== null && isset($autoSizes[$this->cellCollection->getCurrentColumn()])) {
                    
                    $isMerged = isset($isMergeCell[$this->cellCollection->getCurrentCoordinate()]);

                    
                    $isMergedButProceed = false;

                    
                    if ($isMerged && $cell->isMergeRangeValueCell()) {
                        $range = $cell->getMergeRange();
                        $rangeBoundaries = Coordinate::rangeDimension($range);
                        if ($rangeBoundaries[0] == 1) {
                            $isMergedButProceed = true;
                        }
                    }

                    
                    if (!$isMerged || $isMergedButProceed) {
                        
                        
                        $cellValue = NumberFormat::toFormattedString(
                            $cell->getCalculatedValue(),
                            $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode()
                        );

                        $autoSizes[$this->cellCollection->getCurrentColumn()] = max(
                            (float) $autoSizes[$this->cellCollection->getCurrentColumn()],
                            (float) Shared\Font::calculateColumnWidth(
                                $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont(),
                                $cellValue,
                                $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getAlignment()->getTextRotation(),
                                $this->getParent()->getDefaultStyle()->getFont()
                            )
                        );
                    }
                }
            }

            
            foreach ($autoSizes as $columnIndex => $width) {
                if ($width == -1) {
                    $width = $this->getDefaultColumnDimension()->getWidth();
                }
                $this->getColumnDimension($columnIndex)->setWidth($width);
            }
        }

        return $this;
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function rebindParent(Spreadsheet $parent)
    {
        if ($this->parent !== null) {
            $definedNames = $this->parent->getDefinedNames();
            foreach ($definedNames as $definedName) {
                $parent->addDefinedName($definedName);
            }

            $this->parent->removeSheetByIndex(
                $this->parent->getIndex($this)
            );
        }
        $this->parent = $parent;

        return $this;
    }

    
    public function getTitle()
    {
        return $this->title;
    }

    
    public function setTitle($pValue, $updateFormulaCellReferences = true, $validate = true)
    {
        
        if ($this->getTitle() == $pValue) {
            return $this;
        }

        
        $oldTitle = $this->getTitle();

        if ($validate) {
            
            self::checkSheetTitle($pValue);

            if ($this->parent) {
                
                if ($this->parent->sheetNameExists($pValue)) {
                    

                    if (Shared\StringHelper::countCharacters($pValue) > 29) {
                        $pValue = Shared\StringHelper::substring($pValue, 0, 29);
                    }
                    $i = 1;
                    while ($this->parent->sheetNameExists($pValue . ' ' . $i)) {
                        ++$i;
                        if ($i == 10) {
                            if (Shared\StringHelper::countCharacters($pValue) > 28) {
                                $pValue = Shared\StringHelper::substring($pValue, 0, 28);
                            }
                        } elseif ($i == 100) {
                            if (Shared\StringHelper::countCharacters($pValue) > 27) {
                                $pValue = Shared\StringHelper::substring($pValue, 0, 27);
                            }
                        }
                    }

                    $pValue .= " $i";
                }
            }
        }

        
        $this->title = $pValue;
        $this->dirty = true;

        if ($this->parent && $this->parent->getCalculationEngine()) {
            
            $newTitle = $this->getTitle();
            $this->parent->getCalculationEngine()
                ->renameCalculationCacheForWorksheet($oldTitle, $newTitle);
            if ($updateFormulaCellReferences) {
                ReferenceHelper::getInstance()->updateNamedFormulas($this->parent, $oldTitle, $newTitle);
            }
        }

        return $this;
    }

    
    public function getSheetState()
    {
        return $this->sheetState;
    }

    
    public function setSheetState($value)
    {
        $this->sheetState = $value;

        return $this;
    }

    
    public function getPageSetup()
    {
        return $this->pageSetup;
    }

    
    public function setPageSetup(PageSetup $pValue)
    {
        $this->pageSetup = $pValue;

        return $this;
    }

    
    public function getPageMargins()
    {
        return $this->pageMargins;
    }

    
    public function setPageMargins(PageMargins $pValue)
    {
        $this->pageMargins = $pValue;

        return $this;
    }

    
    public function getHeaderFooter()
    {
        return $this->headerFooter;
    }

    
    public function setHeaderFooter(HeaderFooter $pValue)
    {
        $this->headerFooter = $pValue;

        return $this;
    }

    
    public function getSheetView()
    {
        return $this->sheetView;
    }

    
    public function setSheetView(SheetView $pValue)
    {
        $this->sheetView = $pValue;

        return $this;
    }

    
    public function getProtection()
    {
        return $this->protection;
    }

    
    public function setProtection(Protection $pValue)
    {
        $this->protection = $pValue;
        $this->dirty = true;

        return $this;
    }

    
    public function getHighestColumn($row = null)
    {
        if ($row == null) {
            return $this->cachedHighestColumn;
        }

        return $this->getHighestDataColumn($row);
    }

    
    public function getHighestDataColumn($row = null)
    {
        return $this->cellCollection->getHighestColumn($row);
    }

    
    public function getHighestRow($column = null)
    {
        if ($column == null) {
            return $this->cachedHighestRow;
        }

        return $this->getHighestDataRow($column);
    }

    
    public function getHighestDataRow($column = null)
    {
        return $this->cellCollection->getHighestRow($column);
    }

    
    public function getHighestRowAndColumn()
    {
        return $this->cellCollection->getHighestRowAndColumn();
    }

    
    public function setCellValue($pCoordinate, $pValue)
    {
        $this->getCell($pCoordinate)->setValue($pValue);

        return $this;
    }

    
    public function setCellValueByColumnAndRow($columnIndex, $row, $value)
    {
        $this->getCellByColumnAndRow($columnIndex, $row)->setValue($value);

        return $this;
    }

    
    public function setCellValueExplicit($pCoordinate, $pValue, $pDataType)
    {
        
        $this->getCell($pCoordinate)->setValueExplicit($pValue, $pDataType);

        return $this;
    }

    
    public function setCellValueExplicitByColumnAndRow($columnIndex, $row, $value, $dataType)
    {
        $this->getCellByColumnAndRow($columnIndex, $row)->setValueExplicit($value, $dataType);

        return $this;
    }

    
    public function getCell($pCoordinate, $createIfNotExists = true)
    {
        
        $pCoordinateUpper = strtoupper($pCoordinate);

        
        if ($this->cellCollection->has($pCoordinateUpper)) {
            return $this->cellCollection->get($pCoordinateUpper);
        }

        
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = self::extractSheetTitle($pCoordinate, true);

            return $this->parent->getSheetByName($worksheetReference[0])->getCell(strtoupper($worksheetReference[1]), $createIfNotExists);
        }

        
        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) &&
            (preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/i', $pCoordinate, $matches))
        ) {
            $namedRange = DefinedName::resolveName($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getValue();

                return $namedRange->getWorksheet()->getCell($pCoordinate, $createIfNotExists);
            }
        }

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        
        return $createIfNotExists ? $this->createNewCell($pCoordinateUpper) : null;
    }

    
    public function getCellByColumnAndRow($columnIndex, $row, $createIfNotExists = true)
    {
        $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
        $coordinate = $columnLetter . $row;

        if ($this->cellCollection->has($coordinate)) {
            return $this->cellCollection->get($coordinate);
        }

        
        return $createIfNotExists ? $this->createNewCell($coordinate) : null;
    }

    
    private function createNewCell($pCoordinate)
    {
        $cell = new Cell(null, DataType::TYPE_NULL, $this);
        $this->cellCollection->add($pCoordinate, $cell);
        $this->cellCollectionIsSorted = false;

        
        $aCoordinates = Coordinate::coordinateFromString($pCoordinate);
        if (Coordinate::columnIndexFromString($this->cachedHighestColumn) < Coordinate::columnIndexFromString($aCoordinates[0])) {
            $this->cachedHighestColumn = $aCoordinates[0];
        }
        if ($aCoordinates[1] > $this->cachedHighestRow) {
            $this->cachedHighestRow = $aCoordinates[1];
        }

        
        
        $rowDimension = $this->getRowDimension($aCoordinates[1], false);
        $columnDimension = $this->getColumnDimension($aCoordinates[0], false);

        if ($rowDimension !== null && $rowDimension->getXfIndex() > 0) {
            
            $cell->setXfIndex($rowDimension->getXfIndex());
        } elseif ($columnDimension !== null && $columnDimension->getXfIndex() > 0) {
            
            $cell->setXfIndex($columnDimension->getXfIndex());
        }

        return $cell;
    }

    
    public function cellExists($pCoordinate)
    {
        
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = self::extractSheetTitle($pCoordinate, true);

            return $this->parent->getSheetByName($worksheetReference[0])->cellExists(strtoupper($worksheetReference[1]));
        }

        
        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) &&
            (preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/i', $pCoordinate, $matches))
        ) {
            $namedRange = DefinedName::resolveName($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getValue();
                if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
                    if (!$namedRange->getLocalOnly()) {
                        return $namedRange->getWorksheet()->cellExists($pCoordinate);
                    }

                    throw new Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
                }
            } else {
                return false;
            }
        }

        
        $pCoordinate = strtoupper($pCoordinate);

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        
        return $this->cellCollection->has($pCoordinate);
    }

    
    public function cellExistsByColumnAndRow($columnIndex, $row)
    {
        return $this->cellExists(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    
    public function getRowDimension($pRow, $create = true)
    {
        
        $found = null;

        
        if (!isset($this->rowDimensions[$pRow])) {
            if (!$create) {
                return null;
            }
            $this->rowDimensions[$pRow] = new RowDimension($pRow);

            $this->cachedHighestRow = max($this->cachedHighestRow, $pRow);
        }

        return $this->rowDimensions[$pRow];
    }

    
    public function getColumnDimension($pColumn, $create = true)
    {
        
        $pColumn = strtoupper($pColumn);

        
        if (!isset($this->columnDimensions[$pColumn])) {
            if (!$create) {
                return null;
            }
            $this->columnDimensions[$pColumn] = new ColumnDimension($pColumn);

            if (Coordinate::columnIndexFromString($this->cachedHighestColumn) < Coordinate::columnIndexFromString($pColumn)) {
                $this->cachedHighestColumn = $pColumn;
            }
        }

        return $this->columnDimensions[$pColumn];
    }

    
    public function getColumnDimensionByColumn($columnIndex)
    {
        return $this->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex));
    }

    
    public function getStyles()
    {
        return $this->styles;
    }

    
    public function getStyle($pCellCoordinate)
    {
        
        $this->parent->setActiveSheetIndex($this->parent->getIndex($this));

        
        $this->setSelectedCells($pCellCoordinate);

        return $this->parent->getCellXfSupervisor();
    }

    
    public function getConditionalStyles($pCoordinate)
    {
        $pCoordinate = strtoupper($pCoordinate);
        if (!isset($this->conditionalStylesCollection[$pCoordinate])) {
            $this->conditionalStylesCollection[$pCoordinate] = [];
        }

        return $this->conditionalStylesCollection[$pCoordinate];
    }

    
    public function conditionalStylesExists($pCoordinate)
    {
        return isset($this->conditionalStylesCollection[strtoupper($pCoordinate)]);
    }

    
    public function removeConditionalStyles($pCoordinate)
    {
        unset($this->conditionalStylesCollection[strtoupper($pCoordinate)]);

        return $this;
    }

    
    public function getConditionalStylesCollection()
    {
        return $this->conditionalStylesCollection;
    }

    
    public function setConditionalStyles($pCoordinate, $pValue)
    {
        $this->conditionalStylesCollection[strtoupper($pCoordinate)] = $pValue;

        return $this;
    }

    
    public function getStyleByColumnAndRow($columnIndex1, $row1, $columnIndex2 = null, $row2 = null)
    {
        if ($columnIndex2 !== null && $row2 !== null) {
            $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

            return $this->getStyle($cellRange);
        }

        return $this->getStyle(Coordinate::stringFromColumnIndex($columnIndex1) . $row1);
    }

    
    public function duplicateStyle(Style $pCellStyle, $pRange)
    {
        
        $workbook = $this->parent;
        if ($existingStyle = $this->parent->getCellXfByHashCode($pCellStyle->getHashCode())) {
            
            $xfIndex = $existingStyle->getIndex();
        } else {
            
            $workbook->addCellXf($pCellStyle);
            $xfIndex = $pCellStyle->getIndex();
        }

        
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange . ':' . $pRange);

        
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->getCell(Coordinate::stringFromColumnIndex($col) . $row)->setXfIndex($xfIndex);
            }
        }

        return $this;
    }

    
    public function duplicateConditionalStyle(array $pCellStyle, $pRange = '')
    {
        foreach ($pCellStyle as $cellStyle) {
            if (!($cellStyle instanceof Conditional)) {
                throw new Exception('Style is not a conditional style');
            }
        }

        
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange . ':' . $pRange);

        
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(Coordinate::stringFromColumnIndex($col) . $row, $pCellStyle);
            }
        }

        return $this;
    }

    
    public function setBreak($pCoordinate, $pBreak)
    {
        
        $pCoordinate = strtoupper($pCoordinate);

        if ($pCoordinate != '') {
            if ($pBreak == self::BREAK_NONE) {
                if (isset($this->breaks[$pCoordinate])) {
                    unset($this->breaks[$pCoordinate]);
                }
            } else {
                $this->breaks[$pCoordinate] = $pBreak;
            }
        } else {
            throw new Exception('No cell coordinate specified.');
        }

        return $this;
    }

    
    public function setBreakByColumnAndRow($columnIndex, $row, $break)
    {
        return $this->setBreak(Coordinate::stringFromColumnIndex($columnIndex) . $row, $break);
    }

    
    public function getBreaks()
    {
        return $this->breaks;
    }

    
    public function mergeCells($pRange)
    {
        
        $pRange = strtoupper($pRange);

        if (strpos($pRange, ':') !== false) {
            $this->mergeCells[$pRange] = $pRange;

            

            
            $aReferences = Coordinate::extractAllCellReferencesInRange($pRange);

            
            $upperLeft = $aReferences[0];
            if (!$this->cellExists($upperLeft)) {
                $this->getCell($upperLeft)->setValueExplicit(null, DataType::TYPE_NULL);
            }

            
            $count = count($aReferences);
            for ($i = 1; $i < $count; ++$i) {
                if ($this->cellExists($aReferences[$i])) {
                    $this->getCell($aReferences[$i])->setValueExplicit(null, DataType::TYPE_NULL);
                }
            }
        } else {
            throw new Exception('Merge must be set on a range of cells.');
        }

        return $this;
    }

    
    public function mergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

        return $this->mergeCells($cellRange);
    }

    
    public function unmergeCells($pRange)
    {
        
        $pRange = strtoupper($pRange);

        if (strpos($pRange, ':') !== false) {
            if (isset($this->mergeCells[$pRange])) {
                unset($this->mergeCells[$pRange]);
            } else {
                throw new Exception('Cell range ' . $pRange . ' not known as merged.');
            }
        } else {
            throw new Exception('Merge can only be removed from a range of cells.');
        }

        return $this;
    }

    
    public function unmergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

        return $this->unmergeCells($cellRange);
    }

    
    public function getMergeCells()
    {
        return $this->mergeCells;
    }

    
    public function setMergeCells(array $pValue)
    {
        $this->mergeCells = $pValue;

        return $this;
    }

    
    public function protectCells($pRange, $pPassword, $pAlreadyHashed = false)
    {
        
        $pRange = strtoupper($pRange);

        if (!$pAlreadyHashed) {
            $pPassword = Shared\PasswordHasher::hashPassword($pPassword);
        }
        $this->protectedCells[$pRange] = $pPassword;

        return $this;
    }

    
    public function protectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2, $password, $alreadyHashed = false)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

        return $this->protectCells($cellRange, $password, $alreadyHashed);
    }

    
    public function unprotectCells($pRange)
    {
        
        $pRange = strtoupper($pRange);

        if (isset($this->protectedCells[$pRange])) {
            unset($this->protectedCells[$pRange]);
        } else {
            throw new Exception('Cell range ' . $pRange . ' not known as protected.');
        }

        return $this;
    }

    
    public function unprotectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

        return $this->unprotectCells($cellRange);
    }

    
    public function getProtectedCells()
    {
        return $this->protectedCells;
    }

    
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    
    public function setAutoFilter($pValue)
    {
        if (is_string($pValue)) {
            $this->autoFilter->setRange($pValue);
        } elseif (is_object($pValue) && ($pValue instanceof AutoFilter)) {
            $this->autoFilter = $pValue;
        }

        return $this;
    }

    
    public function setAutoFilterByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        return $this->setAutoFilter(
            Coordinate::stringFromColumnIndex($columnIndex1) . $row1
            . ':' .
            Coordinate::stringFromColumnIndex($columnIndex2) . $row2
        );
    }

    
    public function removeAutoFilter()
    {
        $this->autoFilter->setRange(null);

        return $this;
    }

    
    public function getFreezePane()
    {
        return $this->freezePane;
    }

    
    public function freezePane($cell, $topLeftCell = null)
    {
        if (is_string($cell) && Coordinate::coordinateIsRange($cell)) {
            throw new Exception('Freeze pane can not be set on a range of cells.');
        }

        if ($cell !== null && $topLeftCell === null) {
            $coordinate = Coordinate::coordinateFromString($cell);
            $topLeftCell = $coordinate[0] . $coordinate[1];
        }

        $this->freezePane = $cell;
        $this->topLeftCell = $topLeftCell;

        return $this;
    }

    
    public function freezePaneByColumnAndRow($columnIndex, $row)
    {
        return $this->freezePane(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    
    public function unfreezePane()
    {
        return $this->freezePane(null);
    }

    
    public function getTopLeftCell()
    {
        return $this->topLeftCell;
    }

    
    public function insertNewRowBefore($pBefore, $pNumRows = 1)
    {
        if ($pBefore >= 1) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . $pBefore, 0, $pNumRows, $this);
        } else {
            throw new Exception('Rows can only be inserted before at least row 1.');
        }

        return $this;
    }

    
    public function insertNewColumnBefore($pBefore, $pNumCols = 1)
    {
        if (!is_numeric($pBefore)) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pBefore . '1', $pNumCols, 0, $this);
        } else {
            throw new Exception('Column references should not be numeric.');
        }

        return $this;
    }

    
    public function insertNewColumnBeforeByIndex($beforeColumnIndex, $pNumCols = 1)
    {
        if ($beforeColumnIndex >= 1) {
            return $this->insertNewColumnBefore(Coordinate::stringFromColumnIndex($beforeColumnIndex), $pNumCols);
        }

        throw new Exception('Columns can only be inserted before at least column A (1).');
    }

    
    public function removeRow($pRow, $pNumRows = 1)
    {
        if ($pRow < 1) {
            throw new Exception('Rows to be deleted should at least start from row 1.');
        }

        $highestRow = $this->getHighestDataRow();
        $removedRowsCounter = 0;

        for ($r = 0; $r < $pNumRows; ++$r) {
            if ($pRow + $r <= $highestRow) {
                $this->getCellCollection()->removeRow($pRow + $r);
                ++$removedRowsCounter;
            }
        }

        $objReferenceHelper = ReferenceHelper::getInstance();
        $objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
        for ($r = 0; $r < $removedRowsCounter; ++$r) {
            $this->getCellCollection()->removeRow($highestRow);
            --$highestRow;
        }

        return $this;
    }

    
    public function removeColumn($pColumn, $pNumCols = 1)
    {
        if (is_numeric($pColumn)) {
            throw new Exception('Column references should not be numeric.');
        }

        $highestColumn = $this->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $pColumnIndex = Coordinate::columnIndexFromString($pColumn);

        if ($pColumnIndex > $highestColumnIndex) {
            return $this;
        }

        $pColumn = Coordinate::stringFromColumnIndex($pColumnIndex + $pNumCols);
        $objReferenceHelper = ReferenceHelper::getInstance();
        $objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);

        $maxPossibleColumnsToBeRemoved = $highestColumnIndex - $pColumnIndex + 1;

        for ($c = 0, $n = min($maxPossibleColumnsToBeRemoved, $pNumCols); $c < $n; ++$c) {
            $this->getCellCollection()->removeColumn($highestColumn);
            $highestColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($highestColumn) - 1);
        }

        $this->garbageCollect();

        return $this;
    }

    
    public function removeColumnByIndex($columnIndex, $numColumns = 1)
    {
        if ($columnIndex >= 1) {
            return $this->removeColumn(Coordinate::stringFromColumnIndex($columnIndex), $numColumns);
        }

        throw new Exception('Columns to be deleted should at least start from column A (1)');
    }

    
    public function getShowGridlines()
    {
        return $this->showGridlines;
    }

    
    public function setShowGridlines($pValue)
    {
        $this->showGridlines = $pValue;

        return $this;
    }

    
    public function getPrintGridlines()
    {
        return $this->printGridlines;
    }

    
    public function setPrintGridlines($pValue)
    {
        $this->printGridlines = $pValue;

        return $this;
    }

    
    public function getShowRowColHeaders()
    {
        return $this->showRowColHeaders;
    }

    
    public function setShowRowColHeaders($pValue)
    {
        $this->showRowColHeaders = $pValue;

        return $this;
    }

    
    public function getShowSummaryBelow()
    {
        return $this->showSummaryBelow;
    }

    
    public function setShowSummaryBelow($pValue)
    {
        $this->showSummaryBelow = $pValue;

        return $this;
    }

    
    public function getShowSummaryRight()
    {
        return $this->showSummaryRight;
    }

    
    public function setShowSummaryRight($pValue)
    {
        $this->showSummaryRight = $pValue;

        return $this;
    }

    
    public function getComments()
    {
        return $this->comments;
    }

    
    public function setComments(array $pValue)
    {
        $this->comments = $pValue;

        return $this;
    }

    
    public function getComment($pCellCoordinate)
    {
        
        $pCellCoordinate = strtoupper($pCellCoordinate);

        if (Coordinate::coordinateIsRange($pCellCoordinate)) {
            throw new Exception('Cell coordinate string can not be a range of cells.');
        } elseif (strpos($pCellCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate string must not be absolute.');
        } elseif ($pCellCoordinate == '') {
            throw new Exception('Cell coordinate can not be zero-length string.');
        }

        
        if (isset($this->comments[$pCellCoordinate])) {
            return $this->comments[$pCellCoordinate];
        }

        
        $newComment = new Comment();
        $this->comments[$pCellCoordinate] = $newComment;

        return $newComment;
    }

    
    public function getCommentByColumnAndRow($columnIndex, $row)
    {
        return $this->getComment(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    
    public function getActiveCell()
    {
        return $this->activeCell;
    }

    
    public function getSelectedCells()
    {
        return $this->selectedCells;
    }

    
    public function setSelectedCell($pCoordinate)
    {
        return $this->setSelectedCells($pCoordinate);
    }

    
    public function setSelectedCells($pCoordinate)
    {
        
        $pCoordinate = strtoupper($pCoordinate);

        
        $pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);

        
        $pCoordinate = preg_replace('/^(\d+)$/', '${1}:${1}', $pCoordinate);

        
        $pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);

        
        $pCoordinate = preg_replace('/^(\d+):(\d+)$/', 'A${1}:XFD${2}', $pCoordinate);

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            [$first] = Coordinate::splitRange($pCoordinate);
            $this->activeCell = $first[0];
        } else {
            $this->activeCell = $pCoordinate;
        }
        $this->selectedCells = $pCoordinate;

        return $this;
    }

    
    public function setSelectedCellByColumnAndRow($columnIndex, $row)
    {
        return $this->setSelectedCells(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    
    public function getRightToLeft()
    {
        return $this->rightToLeft;
    }

    
    public function setRightToLeft($value)
    {
        $this->rightToLeft = $value;

        return $this;
    }

    
    public function fromArray(array $source, $nullValue = null, $startCell = 'A1', $strictNullComparison = false)
    {
        
        if (!is_array(end($source))) {
            $source = [$source];
        }

        
        [$startColumn, $startRow] = Coordinate::coordinateFromString($startCell);

        
        foreach ($source as $rowData) {
            $currentColumn = $startColumn;
            foreach ($rowData as $cellValue) {
                if ($strictNullComparison) {
                    if ($cellValue !== $nullValue) {
                        
                        $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                    }
                } else {
                    if ($cellValue != $nullValue) {
                        
                        $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                    }
                }
                ++$currentColumn;
            }
            ++$startRow;
        }

        return $this;
    }

    
    public function rangeToArray($pRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        
        $returnValue = [];
        
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $rRef = $returnCellRef ? $row : ++$r;
            $c = -1;
            
            for ($col = $minCol; $col != $maxCol; ++$col) {
                $cRef = $returnCellRef ? $col : ++$c;
                
                
                if ($this->cellCollection->has($col . $row)) {
                    
                    $cell = $this->cellCollection->get($col . $row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof RichText) {
                            $returnValue[$rRef][$cRef] = $cell->getValue()->getPlainText();
                        } else {
                            if ($calculateFormulas) {
                                $returnValue[$rRef][$cRef] = $cell->getCalculatedValue();
                            } else {
                                $returnValue[$rRef][$cRef] = $cell->getValue();
                            }
                        }

                        if ($formatData) {
                            $style = $this->parent->getCellXfByIndex($cell->getXfIndex());
                            $returnValue[$rRef][$cRef] = NumberFormat::toFormattedString(
                                $returnValue[$rRef][$cRef],
                                ($style && $style->getNumberFormat()) ? $style->getNumberFormat()->getFormatCode() : NumberFormat::FORMAT_GENERAL
                            );
                        }
                    } else {
                        
                        $returnValue[$rRef][$cRef] = $nullValue;
                    }
                } else {
                    
                    $returnValue[$rRef][$cRef] = $nullValue;
                }
            }
        }

        
        return $returnValue;
    }

    
    public function namedRangeToArray($pNamedRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $namedRange = DefinedName::resolveName($pNamedRange, $this);
        if ($namedRange !== null) {
            $pWorkSheet = $namedRange->getWorksheet();
            $pCellRange = $namedRange->getValue();

            return $pWorkSheet->rangeToArray($pCellRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
        }

        throw new Exception('Named Range ' . $pNamedRange . ' does not exist.');
    }

    
    public function toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        
        $this->garbageCollect();

        
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();

        
        return $this->rangeToArray('A1:' . $maxCol . $maxRow, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
    }

    
    public function getRowIterator($startRow = 1, $endRow = null)
    {
        return new RowIterator($this, $startRow, $endRow);
    }

    
    public function getColumnIterator($startColumn = 'A', $endColumn = null)
    {
        return new ColumnIterator($this, $startColumn, $endColumn);
    }

    
    public function garbageCollect()
    {
        
        $this->cellCollection->get('A1');

        
        $colRow = $this->cellCollection->getHighestRowAndColumn();
        $highestRow = $colRow['row'];
        $highestColumn = Coordinate::columnIndexFromString($colRow['column']);

        
        foreach ($this->columnDimensions as $dimension) {
            $highestColumn = max($highestColumn, Coordinate::columnIndexFromString($dimension->getColumnIndex()));
        }

        
        foreach ($this->rowDimensions as $dimension) {
            $highestRow = max($highestRow, $dimension->getRowIndex());
        }

        
        if ($highestColumn < 1) {
            $this->cachedHighestColumn = 'A';
        } else {
            $this->cachedHighestColumn = Coordinate::stringFromColumnIndex($highestColumn);
        }
        $this->cachedHighestRow = $highestRow;

        
        return $this;
    }

    
    public function getHashCode()
    {
        if ($this->dirty) {
            $this->hash = md5($this->title . $this->autoFilter . ($this->protection->isProtectionEnabled() ? 't' : 'f') . __CLASS__);
            $this->dirty = false;
        }

        return $this->hash;
    }

    
    public static function extractSheetTitle($pRange, $returnRange = false)
    {
        
        if (($sep = strrpos($pRange, '!')) === false) {
            return $returnRange ? ['', $pRange] : '';
        }

        if ($returnRange) {
            return [substr($pRange, 0, $sep), substr($pRange, $sep + 1)];
        }

        return substr($pRange, $sep + 1);
    }

    
    public function getHyperlink($pCellCoordinate)
    {
        
        if (isset($this->hyperlinkCollection[$pCellCoordinate])) {
            return $this->hyperlinkCollection[$pCellCoordinate];
        }

        
        $this->hyperlinkCollection[$pCellCoordinate] = new Hyperlink();

        return $this->hyperlinkCollection[$pCellCoordinate];
    }

    
    public function setHyperlink($pCellCoordinate, ?Hyperlink $pHyperlink = null)
    {
        if ($pHyperlink === null) {
            unset($this->hyperlinkCollection[$pCellCoordinate]);
        } else {
            $this->hyperlinkCollection[$pCellCoordinate] = $pHyperlink;
        }

        return $this;
    }

    
    public function hyperlinkExists($pCoordinate)
    {
        return isset($this->hyperlinkCollection[$pCoordinate]);
    }

    
    public function getHyperlinkCollection()
    {
        return $this->hyperlinkCollection;
    }

    
    public function getDataValidation($pCellCoordinate)
    {
        
        if (isset($this->dataValidationCollection[$pCellCoordinate])) {
            return $this->dataValidationCollection[$pCellCoordinate];
        }

        
        $this->dataValidationCollection[$pCellCoordinate] = new DataValidation();

        return $this->dataValidationCollection[$pCellCoordinate];
    }

    
    public function setDataValidation($pCellCoordinate, ?DataValidation $pDataValidation = null)
    {
        if ($pDataValidation === null) {
            unset($this->dataValidationCollection[$pCellCoordinate]);
        } else {
            $this->dataValidationCollection[$pCellCoordinate] = $pDataValidation;
        }

        return $this;
    }

    
    public function dataValidationExists($pCoordinate)
    {
        return isset($this->dataValidationCollection[$pCoordinate]);
    }

    
    public function getDataValidationCollection()
    {
        return $this->dataValidationCollection;
    }

    
    public function shrinkRangeToFit($range)
    {
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        $maxCol = Coordinate::columnIndexFromString($maxCol);

        $rangeBlocks = explode(' ', $range);
        foreach ($rangeBlocks as &$rangeSet) {
            $rangeBoundaries = Coordinate::getRangeBoundaries($rangeSet);

            if (Coordinate::columnIndexFromString($rangeBoundaries[0][0]) > $maxCol) {
                $rangeBoundaries[0][0] = Coordinate::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[0][1] > $maxRow) {
                $rangeBoundaries[0][1] = $maxRow;
            }
            if (Coordinate::columnIndexFromString($rangeBoundaries[1][0]) > $maxCol) {
                $rangeBoundaries[1][0] = Coordinate::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[1][1] > $maxRow) {
                $rangeBoundaries[1][1] = $maxRow;
            }
            $rangeSet = $rangeBoundaries[0][0] . $rangeBoundaries[0][1] . ':' . $rangeBoundaries[1][0] . $rangeBoundaries[1][1];
        }
        unset($rangeSet);

        return implode(' ', $rangeBlocks);
    }

    
    public function getTabColor()
    {
        if ($this->tabColor === null) {
            $this->tabColor = new Color();
        }

        return $this->tabColor;
    }

    
    public function resetTabColor()
    {
        $this->tabColor = null;
        $this->tabColor = null;

        return $this;
    }

    
    public function isTabColorSet()
    {
        return $this->tabColor !== null;
    }

    
    public function copy()
    {
        return clone $this;
    }

    
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if ($key == 'parent') {
                continue;
            }

            if (is_object($val) || (is_array($val))) {
                if ($key == 'cellCollection') {
                    $newCollection = $this->cellCollection->cloneCellCollection($this);
                    $this->cellCollection = $newCollection;
                } elseif ($key == 'drawingCollection') {
                    $currentCollection = $this->drawingCollection;
                    $this->drawingCollection = new ArrayObject();
                    foreach ($currentCollection as $item) {
                        if (is_object($item)) {
                            $newDrawing = clone $item;
                            $newDrawing->setWorksheet($this);
                        }
                    }
                } elseif (($key == 'autoFilter') && ($this->autoFilter instanceof AutoFilter)) {
                    $newAutoFilter = clone $this->autoFilter;
                    $this->autoFilter = $newAutoFilter;
                    $this->autoFilter->setParent($this);
                } else {
                    $this->{$key} = unserialize(serialize($val));
                }
            }
        }
    }

    
    public function setCodeName($pValue, $validate = true)
    {
        
        if ($this->getCodeName() == $pValue) {
            return $this;
        }

        if ($validate) {
            $pValue = str_replace(' ', '_', $pValue); 

            
            
            self::checkSheetCodeName($pValue);

            

            if ($this->getParent()) {
                
                if ($this->getParent()->sheetCodeNameExists($pValue)) {
                    

                    if (Shared\StringHelper::countCharacters($pValue) > 29) {
                        $pValue = Shared\StringHelper::substring($pValue, 0, 29);
                    }
                    $i = 1;
                    while ($this->getParent()->sheetCodeNameExists($pValue . '_' . $i)) {
                        ++$i;
                        if ($i == 10) {
                            if (Shared\StringHelper::countCharacters($pValue) > 28) {
                                $pValue = Shared\StringHelper::substring($pValue, 0, 28);
                            }
                        } elseif ($i == 100) {
                            if (Shared\StringHelper::countCharacters($pValue) > 27) {
                                $pValue = Shared\StringHelper::substring($pValue, 0, 27);
                            }
                        }
                    }

                    $pValue .= '_' . $i; 
                }
            }
        }

        $this->codeName = $pValue;

        return $this;
    }

    
    public function getCodeName()
    {
        return $this->codeName;
    }

    
    public function hasCodeName()
    {
        return $this->codeName !== null;
    }
}
