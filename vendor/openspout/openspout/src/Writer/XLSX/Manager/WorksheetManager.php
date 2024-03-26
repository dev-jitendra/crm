<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX as XLSXEscaper;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Manager\RegisteredStyle;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Common\Manager\WorksheetManagerInterface;
use OpenSpout\Writer\XLSX\Helper\DateHelper;
use OpenSpout\Writer\XLSX\Helper\DateIntervalHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\Options;


final class WorksheetManager implements WorksheetManagerInterface
{
    
    public const MAX_CHARACTERS_PER_CELL = 32767;

    
    private readonly CommentsManager $commentsManager;

    private readonly Options $options;

    
    private readonly StyleManager $styleManager;

    
    private readonly StyleMerger $styleMerger;

    
    private readonly SharedStringsManager $sharedStringsManager;

    
    private readonly XLSXEscaper $stringsEscaper;

    
    private readonly StringHelper $stringHelper;

    
    public function __construct(
        Options $options,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        CommentsManager $commentsManager,
        SharedStringsManager $sharedStringsManager,
        XLSXEscaper $stringsEscaper,
        StringHelper $stringHelper
    ) {
        $this->options = $options;
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->commentsManager = $commentsManager;
        $this->sharedStringsManager = $sharedStringsManager;
        $this->stringsEscaper = $stringsEscaper;
        $this->stringHelper = $stringHelper;
    }

    public function getSharedStringsManager(): SharedStringsManager
    {
        return $this->sharedStringsManager;
    }

    public function startSheet(Worksheet $worksheet): void
    {
        $sheetFilePointer = fopen($worksheet->getFilePath(), 'w');
        \assert(false !== $sheetFilePointer);

        $worksheet->setFilePointer($sheetFilePointer);
        $this->commentsManager->createWorksheetCommentFiles($worksheet);
    }

    public function addRow(Worksheet $worksheet, Row $row): void
    {
        if (!$row->isEmpty()) {
            $this->addNonEmptyRow($worksheet, $row);
            $this->commentsManager->addComments($worksheet, $row);
        }

        $worksheet->setLastWrittenRowIndex($worksheet->getLastWrittenRowIndex() + 1);
    }

    public function close(Worksheet $worksheet): void
    {
        $this->commentsManager->closeWorksheetCommentFiles($worksheet);
        fclose($worksheet->getFilePointer());
    }

    
    private function addNonEmptyRow(Worksheet $worksheet, Row $row): void
    {
        $sheetFilePointer = $worksheet->getFilePointer();
        $rowStyle = $row->getStyle();
        $rowIndexOneBased = $worksheet->getLastWrittenRowIndex() + 1;
        $numCells = $row->getNumCells();

        $rowHeight = $row->getHeight();
        $hasCustomHeight = ($this->options->DEFAULT_ROW_HEIGHT > 0 || $rowHeight > 0) ? '1' : '0';
        $rowXML = "<row r=\"{$rowIndexOneBased}\" spans=\"1:{$numCells}\" ".($rowHeight > 0 ? "ht=\"{$rowHeight}\" " : '')."customHeight=\"{$hasCustomHeight}\">";

        foreach ($row->getCells() as $columnIndexZeroBased => $cell) {
            $registeredStyle = $this->applyStyleAndRegister($cell, $rowStyle);
            $cellStyle = $registeredStyle->getStyle();
            if ($registeredStyle->isMatchingRowStyle()) {
                $rowStyle = $cellStyle; 
            }
            $rowXML .= $this->getCellXML($rowIndexOneBased, $columnIndexZeroBased, $cell, $cellStyle->getId());
        }

        $rowXML .= '</row>';

        $wasWriteSuccessful = fwrite($sheetFilePointer, $rowXML);
        if (false === $wasWriteSuccessful) {
            throw new IOException("Unable to write data in {$worksheet->getFilePath()}");
        }
    }

    
    private function applyStyleAndRegister(Cell $cell, Style $rowStyle): RegisteredStyle
    {
        $isMatchingRowStyle = false;
        if ($cell->getStyle()->isEmpty()) {
            $cell->setStyle($rowStyle);

            $possiblyUpdatedStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

            if ($possiblyUpdatedStyle->isUpdated()) {
                $registeredStyle = $this->styleManager->registerStyle($possiblyUpdatedStyle->getStyle());
            } else {
                $registeredStyle = $this->styleManager->registerStyle($rowStyle);
                $isMatchingRowStyle = true;
            }
        } else {
            $mergedCellAndRowStyle = $this->styleMerger->merge($cell->getStyle(), $rowStyle);
            $cell->setStyle($mergedCellAndRowStyle);

            $possiblyUpdatedStyle = $this->styleManager->applyExtraStylesIfNeeded($cell);

            if ($possiblyUpdatedStyle->isUpdated()) {
                $newCellStyle = $possiblyUpdatedStyle->getStyle();
            } else {
                $newCellStyle = $mergedCellAndRowStyle;
            }

            $registeredStyle = $this->styleManager->registerStyle($newCellStyle);
        }

        return new RegisteredStyle($registeredStyle, $isMatchingRowStyle);
    }

    
    private function getCellXML(int $rowIndexOneBased, int $columnIndexZeroBased, Cell $cell, ?int $styleId): string
    {
        $columnLetters = CellHelper::getColumnLettersFromColumnIndex($columnIndexZeroBased);
        $cellXML = '<c r="'.$columnLetters.$rowIndexOneBased.'"';
        $cellXML .= ' s="'.$styleId.'"';

        if ($cell instanceof Cell\StringCell) {
            $cellXML .= $this->getCellXMLFragmentForNonEmptyString($cell->getValue());
        } elseif ($cell instanceof Cell\BooleanCell) {
            $cellXML .= ' t="b"><v>'.(int) $cell->getValue().'</v></c>';
        } elseif ($cell instanceof Cell\NumericCell) {
            $cellXML .= '><v>'.$cell->getValue().'</v></c>';
        } elseif ($cell instanceof Cell\FormulaCell) {
            $cellXML .= '><f>'.substr($cell->getValue(), 1).'</f></c>';
        } elseif ($cell instanceof Cell\DateTimeCell) {
            $cellXML .= '><v>'.DateHelper::toExcel($cell->getValue()).'</v></c>';
        } elseif ($cell instanceof Cell\DateIntervalCell) {
            $cellXML .= '><v>'.DateIntervalHelper::toExcel($cell->getValue()).'</v></c>';
        } elseif ($cell instanceof Cell\ErrorCell) {
            
            $cellXML .= ' t="e"><v>'.$cell->getRawValue().'</v></c>';
        } elseif ($cell instanceof Cell\EmptyCell) {
            if ($this->styleManager->shouldApplyStyleOnEmptyCell($styleId)) {
                $cellXML .= '/>';
            } else {
                
                
                $cellXML = '';
            }
        }

        return $cellXML;
    }

    
    private function getCellXMLFragmentForNonEmptyString(string $cellValue): string
    {
        if ($this->stringHelper->getStringLength($cellValue) > self::MAX_CHARACTERS_PER_CELL) {
            throw new InvalidArgumentException('Trying to add a value that exceeds the maximum number of characters allowed in a cell (32,767)');
        }

        if ($this->options->SHOULD_USE_INLINE_STRINGS) {
            $cellXMLFragment = ' t="inlineStr"><is><t>'.$this->stringsEscaper->escape($cellValue).'</t></is></c>';
        } else {
            $sharedStringId = $this->sharedStringsManager->writeString($cellValue);
            $cellXMLFragment = ' t="s"><v>'.$sharedStringId.'</v></c>';
        }

        return $cellXMLFragment;
    }
}
