<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as PhpspreadsheetWorksheet;

class Worksheet extends WriterPart
{
    
    public function writeWorksheet(PhpspreadsheetWorksheet $pSheet, $pStringTable = null, $includeCharts = false)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $objWriter->startElement('worksheet');
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http:
        $objWriter->writeAttribute('xmlns:r', 'http:

        $objWriter->writeAttribute('xmlns:xdr', 'http:
        $objWriter->writeAttribute('xmlns:x14', 'http:
        $objWriter->writeAttribute('xmlns:mc', 'http:
        $objWriter->writeAttribute('mc:Ignorable', 'x14ac');
        $objWriter->writeAttribute('xmlns:x14ac', 'http:

        
        $this->writeSheetPr($objWriter, $pSheet);

        
        $this->writeDimension($objWriter, $pSheet);

        
        $this->writeSheetViews($objWriter, $pSheet);

        
        $this->writeSheetFormatPr($objWriter, $pSheet);

        
        $this->writeCols($objWriter, $pSheet);

        
        $this->writeSheetData($objWriter, $pSheet, $pStringTable);

        
        $this->writeSheetProtection($objWriter, $pSheet);

        
        $this->writeProtectedRanges($objWriter, $pSheet);

        
        $this->writeAutoFilter($objWriter, $pSheet);

        
        $this->writeMergeCells($objWriter, $pSheet);

        
        $this->writeConditionalFormatting($objWriter, $pSheet);

        
        $this->writeDataValidations($objWriter, $pSheet);

        
        $this->writeHyperlinks($objWriter, $pSheet);

        
        $this->writePrintOptions($objWriter, $pSheet);

        
        $this->writePageMargins($objWriter, $pSheet);

        
        $this->writePageSetup($objWriter, $pSheet);

        
        $this->writeHeaderFooter($objWriter, $pSheet);

        
        $this->writeBreaks($objWriter, $pSheet);

        
        $this->writeDrawings($objWriter, $pSheet, $includeCharts);

        
        $this->writeLegacyDrawing($objWriter, $pSheet);

        
        $this->writeLegacyDrawingHF($objWriter, $pSheet);

        
        $this->writeAlternateContent($objWriter, $pSheet);

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeSheetPr(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('sheetPr');
        if ($pSheet->getParent()->hasMacros()) {
            
            if (!$pSheet->hasCodeName()) {
                $pSheet->setCodeName($pSheet->getTitle());
            }
            $objWriter->writeAttribute('codeName', $pSheet->getCodeName());
        }
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $objWriter->writeAttribute('filterMode', 1);
            $pSheet->getAutoFilter()->showHideRows();
        }

        
        if ($pSheet->isTabColorSet()) {
            $objWriter->startElement('tabColor');
            $objWriter->writeAttribute('rgb', $pSheet->getTabColor()->getARGB());
            $objWriter->endElement();
        }

        
        $objWriter->startElement('outlinePr');
        $objWriter->writeAttribute('summaryBelow', ($pSheet->getShowSummaryBelow() ? '1' : '0'));
        $objWriter->writeAttribute('summaryRight', ($pSheet->getShowSummaryRight() ? '1' : '0'));
        $objWriter->endElement();

        
        if ($pSheet->getPageSetup()->getFitToPage()) {
            $objWriter->startElement('pageSetUpPr');
            $objWriter->writeAttribute('fitToPage', '1');
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    
    private function writeDimension(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('dimension');
        $objWriter->writeAttribute('ref', $pSheet->calculateWorksheetDimension());
        $objWriter->endElement();
    }

    
    private function writeSheetViews(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('sheetViews');

        
        $sheetSelected = false;
        if ($this->getParentWriter()->getSpreadsheet()->getIndex($pSheet) == $this->getParentWriter()->getSpreadsheet()->getActiveSheetIndex()) {
            $sheetSelected = true;
        }

        
        $objWriter->startElement('sheetView');
        $objWriter->writeAttribute('tabSelected', $sheetSelected ? '1' : '0');
        $objWriter->writeAttribute('workbookViewId', '0');

        
        if ($pSheet->getSheetView()->getZoomScale() != 100) {
            $objWriter->writeAttribute('zoomScale', $pSheet->getSheetView()->getZoomScale());
        }
        if ($pSheet->getSheetView()->getZoomScaleNormal() != 100) {
            $objWriter->writeAttribute('zoomScaleNormal', $pSheet->getSheetView()->getZoomScaleNormal());
        }

        
        if ($pSheet->getSheetView()->getShowZeros() === false) {
            $objWriter->writeAttribute('showZeros', 0);
        }

        
        if ($pSheet->getSheetView()->getView() !== SheetView::SHEETVIEW_NORMAL) {
            $objWriter->writeAttribute('view', $pSheet->getSheetView()->getView());
        }

        
        if ($pSheet->getShowGridlines()) {
            $objWriter->writeAttribute('showGridLines', 'true');
        } else {
            $objWriter->writeAttribute('showGridLines', 'false');
        }

        
        if ($pSheet->getShowRowColHeaders()) {
            $objWriter->writeAttribute('showRowColHeaders', '1');
        } else {
            $objWriter->writeAttribute('showRowColHeaders', '0');
        }

        
        if ($pSheet->getRightToLeft()) {
            $objWriter->writeAttribute('rightToLeft', 'true');
        }

        $activeCell = $pSheet->getActiveCell();
        $sqref = $pSheet->getSelectedCells();

        
        $pane = '';
        if ($pSheet->getFreezePane()) {
            [$xSplit, $ySplit] = Coordinate::coordinateFromString($pSheet->getFreezePane());
            $xSplit = Coordinate::columnIndexFromString($xSplit);
            --$xSplit;
            --$ySplit;

            $topLeftCell = $pSheet->getTopLeftCell();

            
            $pane = 'topRight';
            $objWriter->startElement('pane');
            if ($xSplit > 0) {
                $objWriter->writeAttribute('xSplit', $xSplit);
            }
            if ($ySplit > 0) {
                $objWriter->writeAttribute('ySplit', $ySplit);
                $pane = ($xSplit > 0) ? 'bottomRight' : 'bottomLeft';
            }
            $objWriter->writeAttribute('topLeftCell', $topLeftCell);
            $objWriter->writeAttribute('activePane', $pane);
            $objWriter->writeAttribute('state', 'frozen');
            $objWriter->endElement();

            if (($xSplit > 0) && ($ySplit > 0)) {
                
                $objWriter->startElement('selection');
                $objWriter->writeAttribute('pane', 'topRight');
                $objWriter->endElement();
                $objWriter->startElement('selection');
                $objWriter->writeAttribute('pane', 'bottomLeft');
                $objWriter->endElement();
            }
        }

        
        
        
        $objWriter->startElement('selection');
        if ($pane != '') {
            $objWriter->writeAttribute('pane', $pane);
        }
        $objWriter->writeAttribute('activeCell', $activeCell);
        $objWriter->writeAttribute('sqref', $sqref);
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    private function writeSheetFormatPr(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('sheetFormatPr');

        
        if ($pSheet->getDefaultRowDimension()->getRowHeight() >= 0) {
            $objWriter->writeAttribute('customHeight', 'true');
            $objWriter->writeAttribute('defaultRowHeight', StringHelper::formatNumber($pSheet->getDefaultRowDimension()->getRowHeight()));
        } else {
            $objWriter->writeAttribute('defaultRowHeight', '14.4');
        }

        
        if (
            (string) $pSheet->getDefaultRowDimension()->getZeroHeight() === '1' ||
            strtolower((string) $pSheet->getDefaultRowDimension()->getZeroHeight()) == 'true'
        ) {
            $objWriter->writeAttribute('zeroHeight', '1');
        }

        
        if ($pSheet->getDefaultColumnDimension()->getWidth() >= 0) {
            $objWriter->writeAttribute('defaultColWidth', StringHelper::formatNumber($pSheet->getDefaultColumnDimension()->getWidth()));
        }

        
        $outlineLevelRow = 0;
        foreach ($pSheet->getRowDimensions() as $dimension) {
            if ($dimension->getOutlineLevel() > $outlineLevelRow) {
                $outlineLevelRow = $dimension->getOutlineLevel();
            }
        }
        $objWriter->writeAttribute('outlineLevelRow', (int) $outlineLevelRow);

        
        $outlineLevelCol = 0;
        foreach ($pSheet->getColumnDimensions() as $dimension) {
            if ($dimension->getOutlineLevel() > $outlineLevelCol) {
                $outlineLevelCol = $dimension->getOutlineLevel();
            }
        }
        $objWriter->writeAttribute('outlineLevelCol', (int) $outlineLevelCol);

        $objWriter->endElement();
    }

    
    private function writeCols(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        if (count($pSheet->getColumnDimensions()) > 0) {
            $objWriter->startElement('cols');

            $pSheet->calculateColumnWidths();

            
            foreach ($pSheet->getColumnDimensions() as $colDimension) {
                
                $objWriter->startElement('col');
                $objWriter->writeAttribute('min', Coordinate::columnIndexFromString($colDimension->getColumnIndex()));
                $objWriter->writeAttribute('max', Coordinate::columnIndexFromString($colDimension->getColumnIndex()));

                if ($colDimension->getWidth() < 0) {
                    
                    $objWriter->writeAttribute('width', '9.10');
                } else {
                    
                    $objWriter->writeAttribute('width', StringHelper::formatNumber($colDimension->getWidth()));
                }

                
                if ($colDimension->getVisible() === false) {
                    $objWriter->writeAttribute('hidden', 'true');
                }

                
                if ($colDimension->getAutoSize()) {
                    $objWriter->writeAttribute('bestFit', 'true');
                }

                
                if ($colDimension->getWidth() != $pSheet->getDefaultColumnDimension()->getWidth()) {
                    $objWriter->writeAttribute('customWidth', 'true');
                }

                
                if ($colDimension->getCollapsed() === true) {
                    $objWriter->writeAttribute('collapsed', 'true');
                }

                
                if ($colDimension->getOutlineLevel() > 0) {
                    $objWriter->writeAttribute('outlineLevel', $colDimension->getOutlineLevel());
                }

                
                $objWriter->writeAttribute('style', $colDimension->getXfIndex());

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writeSheetProtection(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('sheetProtection');

        $protection = $pSheet->getProtection();

        if ($protection->getAlgorithm()) {
            $objWriter->writeAttribute('algorithmName', $protection->getAlgorithm());
            $objWriter->writeAttribute('hashValue', $protection->getPassword());
            $objWriter->writeAttribute('saltValue', $protection->getSalt());
            $objWriter->writeAttribute('spinCount', $protection->getSpinCount());
        } elseif ($protection->getPassword() !== '') {
            $objWriter->writeAttribute('password', $protection->getPassword());
        }

        $objWriter->writeAttribute('sheet', ($protection->getSheet() ? 'true' : 'false'));
        $objWriter->writeAttribute('objects', ($protection->getObjects() ? 'true' : 'false'));
        $objWriter->writeAttribute('scenarios', ($protection->getScenarios() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatCells', ($protection->getFormatCells() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatColumns', ($protection->getFormatColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('formatRows', ($protection->getFormatRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertColumns', ($protection->getInsertColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertRows', ($protection->getInsertRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('insertHyperlinks', ($protection->getInsertHyperlinks() ? 'true' : 'false'));
        $objWriter->writeAttribute('deleteColumns', ($protection->getDeleteColumns() ? 'true' : 'false'));
        $objWriter->writeAttribute('deleteRows', ($protection->getDeleteRows() ? 'true' : 'false'));
        $objWriter->writeAttribute('selectLockedCells', ($protection->getSelectLockedCells() ? 'true' : 'false'));
        $objWriter->writeAttribute('sort', ($protection->getSort() ? 'true' : 'false'));
        $objWriter->writeAttribute('autoFilter', ($protection->getAutoFilter() ? 'true' : 'false'));
        $objWriter->writeAttribute('pivotTables', ($protection->getPivotTables() ? 'true' : 'false'));
        $objWriter->writeAttribute('selectUnlockedCells', ($protection->getSelectUnlockedCells() ? 'true' : 'false'));
        $objWriter->endElement();
    }

    private static function writeAttributeIf(XMLWriter $objWriter, $condition, string $attr, string $val): void
    {
        if ($condition) {
            $objWriter->writeAttribute($attr, $val);
        }
    }

    private static function writeElementIf(XMLWriter $objWriter, $condition, string $attr, string $val): void
    {
        if ($condition) {
            $objWriter->writeElement($attr, $val);
        }
    }

    private static function writeOtherCondElements(XMLWriter $objWriter, Conditional $conditional, string $cellCoordinate): void
    {
        if (
            $conditional->getConditionType() == Conditional::CONDITION_CELLIS
            || $conditional->getConditionType() == Conditional::CONDITION_CONTAINSTEXT
            || $conditional->getConditionType() == Conditional::CONDITION_EXPRESSION
        ) {
            foreach ($conditional->getConditions() as $formula) {
                
                $objWriter->writeElement('formula', Xlfn::addXlfn($formula));
            }
        } elseif ($conditional->getConditionType() == Conditional::CONDITION_CONTAINSBLANKS) {
            
            $objWriter->writeElement('formula', 'LEN(TRIM(' . $cellCoordinate . '))=0');
        } elseif ($conditional->getConditionType() == Conditional::CONDITION_NOTCONTAINSBLANKS) {
            
            $objWriter->writeElement('formula', 'LEN(TRIM(' . $cellCoordinate . '))>0');
        }
    }

    private static function writeTextCondElements(XMLWriter $objWriter, Conditional $conditional, string $cellCoordinate): void
    {
        $txt = $conditional->getText();
        if ($txt !== null) {
            $objWriter->writeAttribute('text', $txt);
            if ($conditional->getOperatorType() == Conditional::OPERATOR_CONTAINSTEXT) {
                $objWriter->writeElement('formula', 'NOT(ISERROR(SEARCH("' . $txt . '",' . $cellCoordinate . ')))');
            } elseif ($conditional->getOperatorType() == Conditional::OPERATOR_BEGINSWITH) {
                $objWriter->writeElement('formula', 'LEFT(' . $cellCoordinate . ',' . strlen($txt) . ')="' . $txt . '"');
            } elseif ($conditional->getOperatorType() == Conditional::OPERATOR_ENDSWITH) {
                $objWriter->writeElement('formula', 'RIGHT(' . $cellCoordinate . ',' . strlen($txt) . ')="' . $txt . '"');
            } elseif ($conditional->getOperatorType() == Conditional::OPERATOR_NOTCONTAINS) {
                $objWriter->writeElement('formula', 'ISERROR(SEARCH("' . $txt . '",' . $cellCoordinate . '))');
            }
        }
    }

    
    private function writeConditionalFormatting(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $id = 1;

        
        foreach ($pSheet->getConditionalStylesCollection() as $cellCoordinate => $conditionalStyles) {
            foreach ($conditionalStyles as $conditional) {
                
                
                
                
                if ($conditional->getConditionType() != Conditional::CONDITION_NONE) {
                    
                    $objWriter->startElement('conditionalFormatting');
                    $objWriter->writeAttribute('sqref', $cellCoordinate);

                    
                    $objWriter->startElement('cfRule');
                    $objWriter->writeAttribute('type', $conditional->getConditionType());
                    $objWriter->writeAttribute('dxfId', $this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode($conditional->getHashCode()));
                    $objWriter->writeAttribute('priority', $id++);

                    self::writeAttributeif(
                        $objWriter,
                        ($conditional->getConditionType() == Conditional::CONDITION_CELLIS || $conditional->getConditionType() == Conditional::CONDITION_CONTAINSTEXT)
                        && $conditional->getOperatorType() != Conditional::OPERATOR_NONE,
                        'operator',
                        $conditional->getOperatorType()
                    );

                    self::writeAttributeIf($objWriter, $conditional->getStopIfTrue(), 'stopIfTrue', '1');

                    if ($conditional->getConditionType() == Conditional::CONDITION_CONTAINSTEXT) {
                        self::writeTextCondElements($objWriter, $conditional, $cellCoordinate);
                    } else {
                        self::writeOtherCondElements($objWriter, $conditional, $cellCoordinate);
                    }

                    $objWriter->endElement();

                    $objWriter->endElement();
                }
            }
        }
    }

    
    private function writeDataValidations(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $dataValidationCollection = $pSheet->getDataValidationCollection();

        
        if (!empty($dataValidationCollection)) {
            $dataValidationCollection = Coordinate::mergeRangesInCollection($dataValidationCollection);
            $objWriter->startElement('dataValidations');
            $objWriter->writeAttribute('count', count($dataValidationCollection));

            foreach ($dataValidationCollection as $coordinate => $dv) {
                $objWriter->startElement('dataValidation');

                if ($dv->getType() != '') {
                    $objWriter->writeAttribute('type', $dv->getType());
                }

                if ($dv->getErrorStyle() != '') {
                    $objWriter->writeAttribute('errorStyle', $dv->getErrorStyle());
                }

                if ($dv->getOperator() != '') {
                    $objWriter->writeAttribute('operator', $dv->getOperator());
                }

                $objWriter->writeAttribute('allowBlank', ($dv->getAllowBlank() ? '1' : '0'));
                $objWriter->writeAttribute('showDropDown', (!$dv->getShowDropDown() ? '1' : '0'));
                $objWriter->writeAttribute('showInputMessage', ($dv->getShowInputMessage() ? '1' : '0'));
                $objWriter->writeAttribute('showErrorMessage', ($dv->getShowErrorMessage() ? '1' : '0'));

                if ($dv->getErrorTitle() !== '') {
                    $objWriter->writeAttribute('errorTitle', $dv->getErrorTitle());
                }
                if ($dv->getError() !== '') {
                    $objWriter->writeAttribute('error', $dv->getError());
                }
                if ($dv->getPromptTitle() !== '') {
                    $objWriter->writeAttribute('promptTitle', $dv->getPromptTitle());
                }
                if ($dv->getPrompt() !== '') {
                    $objWriter->writeAttribute('prompt', $dv->getPrompt());
                }

                $objWriter->writeAttribute('sqref', $coordinate);

                if ($dv->getFormula1() !== '') {
                    $objWriter->writeElement('formula1', $dv->getFormula1());
                }
                if ($dv->getFormula2() !== '') {
                    $objWriter->writeElement('formula2', $dv->getFormula2());
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writeHyperlinks(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $hyperlinkCollection = $pSheet->getHyperlinkCollection();

        
        $relationId = 1;

        
        if (!empty($hyperlinkCollection)) {
            $objWriter->startElement('hyperlinks');

            foreach ($hyperlinkCollection as $coordinate => $hyperlink) {
                $objWriter->startElement('hyperlink');

                $objWriter->writeAttribute('ref', $coordinate);
                if (!$hyperlink->isInternal()) {
                    $objWriter->writeAttribute('r:id', 'rId_hyperlink_' . $relationId);
                    ++$relationId;
                } else {
                    $objWriter->writeAttribute('location', str_replace('sheet:
                }

                if ($hyperlink->getTooltip() !== '') {
                    $objWriter->writeAttribute('tooltip', $hyperlink->getTooltip());
                    $objWriter->writeAttribute('display', $hyperlink->getTooltip());
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writeProtectedRanges(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        if (count($pSheet->getProtectedCells()) > 0) {
            
            $objWriter->startElement('protectedRanges');

            
            foreach ($pSheet->getProtectedCells() as $protectedCell => $passwordHash) {
                
                $objWriter->startElement('protectedRange');
                $objWriter->writeAttribute('name', 'p' . md5($protectedCell));
                $objWriter->writeAttribute('sqref', $protectedCell);
                if (!empty($passwordHash)) {
                    $objWriter->writeAttribute('password', $passwordHash);
                }
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writeMergeCells(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        if (count($pSheet->getMergeCells()) > 0) {
            
            $objWriter->startElement('mergeCells');

            
            foreach ($pSheet->getMergeCells() as $mergeCell) {
                
                $objWriter->startElement('mergeCell');
                $objWriter->writeAttribute('ref', $mergeCell);
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writePrintOptions(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('printOptions');

        $objWriter->writeAttribute('gridLines', ($pSheet->getPrintGridlines() ? 'true' : 'false'));
        $objWriter->writeAttribute('gridLinesSet', 'true');

        if ($pSheet->getPageSetup()->getHorizontalCentered()) {
            $objWriter->writeAttribute('horizontalCentered', 'true');
        }

        if ($pSheet->getPageSetup()->getVerticalCentered()) {
            $objWriter->writeAttribute('verticalCentered', 'true');
        }

        $objWriter->endElement();
    }

    
    private function writePageMargins(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('pageMargins');
        $objWriter->writeAttribute('left', StringHelper::formatNumber($pSheet->getPageMargins()->getLeft()));
        $objWriter->writeAttribute('right', StringHelper::formatNumber($pSheet->getPageMargins()->getRight()));
        $objWriter->writeAttribute('top', StringHelper::formatNumber($pSheet->getPageMargins()->getTop()));
        $objWriter->writeAttribute('bottom', StringHelper::formatNumber($pSheet->getPageMargins()->getBottom()));
        $objWriter->writeAttribute('header', StringHelper::formatNumber($pSheet->getPageMargins()->getHeader()));
        $objWriter->writeAttribute('footer', StringHelper::formatNumber($pSheet->getPageMargins()->getFooter()));
        $objWriter->endElement();
    }

    
    private function writeAutoFilter(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        $autoFilterRange = $pSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            
            $objWriter->startElement('autoFilter');

            
            $range = Coordinate::splitRange($autoFilterRange);
            $range = $range[0];
            
            [$ws, $range[0]] = PhpspreadsheetWorksheet::extractSheetTitle($range[0], true);
            $range = implode(':', $range);

            $objWriter->writeAttribute('ref', str_replace('$', '', $range));

            $columns = $pSheet->getAutoFilter()->getColumns();
            if (count($columns) > 0) {
                foreach ($columns as $columnID => $column) {
                    $rules = $column->getRules();
                    if (count($rules) > 0) {
                        $objWriter->startElement('filterColumn');
                        $objWriter->writeAttribute('colId', $pSheet->getAutoFilter()->getColumnOffset($columnID));

                        $objWriter->startElement($column->getFilterType());
                        if ($column->getJoin() == Column::AUTOFILTER_COLUMN_JOIN_AND) {
                            $objWriter->writeAttribute('and', 1);
                        }

                        foreach ($rules as $rule) {
                            if (
                                ($column->getFilterType() === Column::AUTOFILTER_FILTERTYPE_FILTER) &&
                                ($rule->getOperator() === Rule::AUTOFILTER_COLUMN_RULE_EQUAL) &&
                                ($rule->getValue() === '')
                            ) {
                                
                                $objWriter->writeAttribute('blank', 1);
                            } elseif ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER) {
                                
                                $objWriter->writeAttribute('type', $rule->getGrouping());
                                $val = $column->getAttribute('val');
                                if ($val !== null) {
                                    $objWriter->writeAttribute('val', $val);
                                }
                                $maxVal = $column->getAttribute('maxVal');
                                if ($maxVal !== null) {
                                    $objWriter->writeAttribute('maxVal', $maxVal);
                                }
                            } elseif ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_TOPTENFILTER) {
                                
                                $objWriter->writeAttribute('val', $rule->getValue());
                                $objWriter->writeAttribute('percent', (($rule->getOperator() === Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT) ? '1' : '0'));
                                $objWriter->writeAttribute('top', (($rule->getGrouping() === Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP) ? '1' : '0'));
                            } else {
                                
                                $objWriter->startElement($rule->getRuleType());

                                if ($rule->getOperator() !== Rule::AUTOFILTER_COLUMN_RULE_EQUAL) {
                                    $objWriter->writeAttribute('operator', $rule->getOperator());
                                }
                                if ($rule->getRuleType() === Rule::AUTOFILTER_RULETYPE_DATEGROUP) {
                                    
                                    foreach ($rule->getValue() as $key => $value) {
                                        if ($value > '') {
                                            $objWriter->writeAttribute($key, $value);
                                        }
                                    }
                                    $objWriter->writeAttribute('dateTimeGrouping', $rule->getGrouping());
                                } else {
                                    $objWriter->writeAttribute('val', $rule->getValue());
                                }

                                $objWriter->endElement();
                            }
                        }

                        $objWriter->endElement();

                        $objWriter->endElement();
                    }
                }
            }
            $objWriter->endElement();
        }
    }

    
    private function writePageSetup(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('pageSetup');
        $objWriter->writeAttribute('paperSize', $pSheet->getPageSetup()->getPaperSize());
        $objWriter->writeAttribute('orientation', $pSheet->getPageSetup()->getOrientation());

        if ($pSheet->getPageSetup()->getScale() !== null) {
            $objWriter->writeAttribute('scale', $pSheet->getPageSetup()->getScale());
        }
        if ($pSheet->getPageSetup()->getFitToHeight() !== null) {
            $objWriter->writeAttribute('fitToHeight', $pSheet->getPageSetup()->getFitToHeight());
        } else {
            $objWriter->writeAttribute('fitToHeight', '0');
        }
        if ($pSheet->getPageSetup()->getFitToWidth() !== null) {
            $objWriter->writeAttribute('fitToWidth', $pSheet->getPageSetup()->getFitToWidth());
        } else {
            $objWriter->writeAttribute('fitToWidth', '0');
        }
        if ($pSheet->getPageSetup()->getFirstPageNumber() !== null) {
            $objWriter->writeAttribute('firstPageNumber', $pSheet->getPageSetup()->getFirstPageNumber());
            $objWriter->writeAttribute('useFirstPageNumber', '1');
        }
        $objWriter->writeAttribute('pageOrder', $pSheet->getPageSetup()->getPageOrder());

        $getUnparsedLoadedData = $pSheet->getParent()->getUnparsedLoadedData();
        if (isset($getUnparsedLoadedData['sheets'][$pSheet->getCodeName()]['pageSetupRelId'])) {
            $objWriter->writeAttribute('r:id', $getUnparsedLoadedData['sheets'][$pSheet->getCodeName()]['pageSetupRelId']);
        }

        $objWriter->endElement();
    }

    
    private function writeHeaderFooter(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $objWriter->startElement('headerFooter');
        $objWriter->writeAttribute('differentOddEven', ($pSheet->getHeaderFooter()->getDifferentOddEven() ? 'true' : 'false'));
        $objWriter->writeAttribute('differentFirst', ($pSheet->getHeaderFooter()->getDifferentFirst() ? 'true' : 'false'));
        $objWriter->writeAttribute('scaleWithDoc', ($pSheet->getHeaderFooter()->getScaleWithDocument() ? 'true' : 'false'));
        $objWriter->writeAttribute('alignWithMargins', ($pSheet->getHeaderFooter()->getAlignWithMargins() ? 'true' : 'false'));

        $objWriter->writeElement('oddHeader', $pSheet->getHeaderFooter()->getOddHeader());
        $objWriter->writeElement('oddFooter', $pSheet->getHeaderFooter()->getOddFooter());
        $objWriter->writeElement('evenHeader', $pSheet->getHeaderFooter()->getEvenHeader());
        $objWriter->writeElement('evenFooter', $pSheet->getHeaderFooter()->getEvenFooter());
        $objWriter->writeElement('firstHeader', $pSheet->getHeaderFooter()->getFirstHeader());
        $objWriter->writeElement('firstFooter', $pSheet->getHeaderFooter()->getFirstFooter());
        $objWriter->endElement();
    }

    
    private function writeBreaks(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        $aRowBreaks = [];
        $aColumnBreaks = [];
        foreach ($pSheet->getBreaks() as $cell => $breakType) {
            if ($breakType == PhpspreadsheetWorksheet::BREAK_ROW) {
                $aRowBreaks[] = $cell;
            } elseif ($breakType == PhpspreadsheetWorksheet::BREAK_COLUMN) {
                $aColumnBreaks[] = $cell;
            }
        }

        
        if (!empty($aRowBreaks)) {
            $objWriter->startElement('rowBreaks');
            $objWriter->writeAttribute('count', count($aRowBreaks));
            $objWriter->writeAttribute('manualBreakCount', count($aRowBreaks));

            foreach ($aRowBreaks as $cell) {
                $coords = Coordinate::coordinateFromString($cell);

                $objWriter->startElement('brk');
                $objWriter->writeAttribute('id', $coords[1]);
                $objWriter->writeAttribute('man', '1');
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }

        
        if (!empty($aColumnBreaks)) {
            $objWriter->startElement('colBreaks');
            $objWriter->writeAttribute('count', count($aColumnBreaks));
            $objWriter->writeAttribute('manualBreakCount', count($aColumnBreaks));

            foreach ($aColumnBreaks as $cell) {
                $coords = Coordinate::coordinateFromString($cell);

                $objWriter->startElement('brk');
                $objWriter->writeAttribute('id', Coordinate::columnIndexFromString($coords[0]) - 1);
                $objWriter->writeAttribute('man', '1');
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }
    }

    
    private function writeSheetData(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet, array $pStringTable): void
    {
        
        $aFlippedStringTable = $this->getParentWriter()->getWriterPart('stringtable')->flipStringTable($pStringTable);

        
        $objWriter->startElement('sheetData');

        
        $colCount = Coordinate::columnIndexFromString($pSheet->getHighestColumn());

        
        $highestRow = $pSheet->getHighestRow();

        
        $cellsByRow = [];
        foreach ($pSheet->getCoordinates() as $coordinate) {
            $cellAddress = Coordinate::coordinateFromString($coordinate);
            $cellsByRow[$cellAddress[1]][] = $coordinate;
        }

        $currentRow = 0;
        while ($currentRow++ < $highestRow) {
            
            $rowDimension = $pSheet->getRowDimension($currentRow);

            
            $writeCurrentRow = isset($cellsByRow[$currentRow]) || $rowDimension->getRowHeight() >= 0 || $rowDimension->getVisible() == false || $rowDimension->getCollapsed() == true || $rowDimension->getOutlineLevel() > 0 || $rowDimension->getXfIndex() !== null;

            if ($writeCurrentRow) {
                
                $objWriter->startElement('row');
                $objWriter->writeAttribute('r', $currentRow);
                $objWriter->writeAttribute('spans', '1:' . $colCount);

                
                if ($rowDimension->getRowHeight() >= 0) {
                    $objWriter->writeAttribute('customHeight', '1');
                    $objWriter->writeAttribute('ht', StringHelper::formatNumber($rowDimension->getRowHeight()));
                }

                
                if (!$rowDimension->getVisible() === true) {
                    $objWriter->writeAttribute('hidden', 'true');
                }

                
                if ($rowDimension->getCollapsed() === true) {
                    $objWriter->writeAttribute('collapsed', 'true');
                }

                
                if ($rowDimension->getOutlineLevel() > 0) {
                    $objWriter->writeAttribute('outlineLevel', $rowDimension->getOutlineLevel());
                }

                
                if ($rowDimension->getXfIndex() !== null) {
                    $objWriter->writeAttribute('s', $rowDimension->getXfIndex());
                    $objWriter->writeAttribute('customFormat', '1');
                }

                
                if (isset($cellsByRow[$currentRow])) {
                    foreach ($cellsByRow[$currentRow] as $cellAddress) {
                        
                        $this->writeCell($objWriter, $pSheet, $cellAddress, $aFlippedStringTable);
                    }
                }

                
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();
    }

    
    private function writeCellInlineStr(XMLWriter $objWriter, string $mappedType, $cellValue): void
    {
        $objWriter->writeAttribute('t', $mappedType);
        if (!$cellValue instanceof RichText) {
            $objWriter->writeElement('t', StringHelper::controlCharacterPHP2OOXML(htmlspecialchars($cellValue)));
        } elseif ($cellValue instanceof RichText) {
            $objWriter->startElement('is');
            $this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $cellValue);
            $objWriter->endElement();
        }
    }

    
    private function writeCellString(XMLWriter $objWriter, string $mappedType, $cellValue, array $pFlippedStringTable): void
    {
        $objWriter->writeAttribute('t', $mappedType);
        if (!$cellValue instanceof RichText) {
            self::writeElementIf($objWriter, isset($pFlippedStringTable[$cellValue]), 'v', $pFlippedStringTable[$cellValue] ?? '');
        } else {
            $objWriter->writeElement('v', $pFlippedStringTable[$cellValue->getHashCode()]);
        }
    }

    
    private function writeCellNumeric(XMLWriter $objWriter, $cellValue): void
    {
        
        if (is_float($cellValue)) {
            
            $cellValue = str_replace(',', '.', (string) $cellValue);
            if (strpos($cellValue, '.') === false) {
                $cellValue = $cellValue . '.0';
            }
        }
        $objWriter->writeElement('v', $cellValue);
    }

    private function writeCellBoolean(XMLWriter $objWriter, string $mappedType, bool $cellValue): void
    {
        $objWriter->writeAttribute('t', $mappedType);
        $objWriter->writeElement('v', $cellValue ? '1' : '0');
    }

    private function writeCellError(XMLWriter $objWriter, string $mappedType, string $cellValue, string $formulaerr = '#NULL!'): void
    {
        $objWriter->writeAttribute('t', $mappedType);
        $cellIsFormula = substr($cellValue, 0, 1) === '=';
        self::writeElementIf($objWriter, $cellIsFormula, 'f', Xlfn::addXlfnStripEquals($cellValue));
        $objWriter->writeElement('v', $cellIsFormula ? $formulaerr : $cellValue);
    }

    private function writeCellFormula(XMLWriter $objWriter, string $cellValue, Cell $pCell): void
    {
        $calculatedValue = $this->getParentWriter()->getPreCalculateFormulas() ? $pCell->getCalculatedValue() : $cellValue;
        if (is_string($calculatedValue)) {
            if (\PhpOffice\PhpSpreadsheet\Calculation\Functions::isError($calculatedValue)) {
                $this->writeCellError($objWriter, 'e', $cellValue, $calculatedValue);

                return;
            }
            $objWriter->writeAttribute('t', 'str');
        } elseif (is_bool($calculatedValue)) {
            $objWriter->writeAttribute('t', 'b');
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        $objWriter->writeElement('f', Xlfn::addXlfnStripEquals($cellValue));
        self::writeElementIf(
            $objWriter,
            $this->getParentWriter()->getOffice2003Compatibility() === false,
            'v',
            ($this->getParentWriter()->getPreCalculateFormulas() && !is_array($calculatedValue) && substr($calculatedValue, 0, 1) !== '#')
                ? StringHelper::formatNumber($calculatedValue) : '0'
        );
    }

    
    private function writeCell(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet, string $pCellAddress, array $pFlippedStringTable): void
    {
        
        $pCell = $pSheet->getCell($pCellAddress);
        $objWriter->startElement('c');
        $objWriter->writeAttribute('r', $pCellAddress);

        
        $xfi = $pCell->getXfIndex();
        self::writeAttributeIf($objWriter, $xfi, 's', $xfi);

        
        $cellValue = $pCell->getValue();
        if (is_object($cellValue) || $cellValue !== '') {
            
            $mappedType = $pCell->getDataType();

            
            switch (strtolower($mappedType)) {
                case 'inlinestr':    
                    $this->writeCellInlineStr($objWriter, $mappedType, $cellValue);

                    break;
                case 's':            
                    $this->writeCellString($objWriter, $mappedType, $cellValue, $pFlippedStringTable);

                    break;
                case 'f':            
                    $this->writeCellFormula($objWriter, $cellValue, $pCell);

                    break;
                case 'n':            
                    $this->writeCellNumeric($objWriter, $cellValue);

                    break;
                case 'b':            
                    $this->writeCellBoolean($objWriter, $mappedType, $cellValue);

                    break;
                case 'e':            
                    $this->writeCellError($objWriter, $mappedType, $cellValue);
            }
        }

        $objWriter->endElement();
    }

    
    private function writeDrawings(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet, $includeCharts = false): void
    {
        $unparsedLoadedData = $pSheet->getParent()->getUnparsedLoadedData();
        $hasUnparsedDrawing = isset($unparsedLoadedData['sheets'][$pSheet->getCodeName()]['drawingOriginalIds']);
        $chartCount = ($includeCharts) ? $pSheet->getChartCollection()->count() : 0;
        if ($chartCount == 0 && $pSheet->getDrawingCollection()->count() == 0 && !$hasUnparsedDrawing) {
            return;
        }

        
        $objWriter->startElement('drawing');

        $rId = 'rId1';
        if (isset($unparsedLoadedData['sheets'][$pSheet->getCodeName()]['drawingOriginalIds'])) {
            $drawingOriginalIds = $unparsedLoadedData['sheets'][$pSheet->getCodeName()]['drawingOriginalIds'];
            
            
            $rId = reset($drawingOriginalIds);
        }

        $objWriter->writeAttribute('r:id', $rId);
        $objWriter->endElement();
    }

    
    private function writeLegacyDrawing(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        if (count($pSheet->getComments()) > 0) {
            $objWriter->startElement('legacyDrawing');
            $objWriter->writeAttribute('r:id', 'rId_comments_vml1');
            $objWriter->endElement();
        }
    }

    
    private function writeLegacyDrawingHF(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        
        if (count($pSheet->getHeaderFooter()->getImages()) > 0) {
            $objWriter->startElement('legacyDrawingHF');
            $objWriter->writeAttribute('r:id', 'rId_headerfooter_vml1');
            $objWriter->endElement();
        }
    }

    private function writeAlternateContent(XMLWriter $objWriter, PhpspreadsheetWorksheet $pSheet): void
    {
        if (empty($pSheet->getParent()->getUnparsedLoadedData()['sheets'][$pSheet->getCodeName()]['AlternateContents'])) {
            return;
        }

        foreach ($pSheet->getParent()->getUnparsedLoadedData()['sheets'][$pSheet->getCodeName()]['AlternateContents'] as $alternateContent) {
            $objWriter->writeRaw($alternateContent);
        }
    }
}
