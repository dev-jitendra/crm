<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Manager;

use DateTimeImmutable;
use DateTimeInterface;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\ODS as ODSEscaper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Manager\RegisteredStyle;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\Common\Manager\WorksheetManagerInterface;
use OpenSpout\Writer\ODS\Manager\Style\StyleManager;


final class WorksheetManager implements WorksheetManagerInterface
{
    
    private readonly ODSEscaper $stringsEscaper;

    
    private readonly StyleManager $styleManager;

    
    private readonly StyleMerger $styleMerger;

    
    public function __construct(
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        ODSEscaper $stringsEscaper
    ) {
        $this->styleManager = $styleManager;
        $this->styleMerger = $styleMerger;
        $this->stringsEscaper = $stringsEscaper;
    }

    
    public function startSheet(Worksheet $worksheet): void
    {
        $sheetFilePointer = fopen($worksheet->getFilePath(), 'w');
        \assert(false !== $sheetFilePointer);

        $worksheet->setFilePointer($sheetFilePointer);
    }

    
    public function getTableElementStartAsString(Worksheet $worksheet): string
    {
        $externalSheet = $worksheet->getExternalSheet();
        $escapedSheetName = $this->stringsEscaper->escape($externalSheet->getName());
        $tableStyleName = 'ta'.($externalSheet->getIndex() + 1);

        $tableElement = '<table:table table:style-name="'.$tableStyleName.'" table:name="'.$escapedSheetName.'">';
        $tableElement .= $this->styleManager->getStyledTableColumnXMLContent($worksheet->getMaxNumColumns());

        return $tableElement;
    }

    
    public function getTableDatabaseRangeElementAsString(Worksheet $worksheet): string
    {
        $externalSheet = $worksheet->getExternalSheet();
        $escapedSheetName = $this->stringsEscaper->escape($externalSheet->getName());
        $databaseRange = '';

        if (null !== $autofilter = $externalSheet->getAutoFilter()) {
            $rangeAddress = sprintf(
                '\'%s\'.%s%s:\'%s\'.%s%s',
                $escapedSheetName,
                CellHelper::getColumnLettersFromColumnIndex($autofilter->fromColumnIndex),
                $autofilter->fromRow,
                $escapedSheetName,
                CellHelper::getColumnLettersFromColumnIndex($autofilter->toColumnIndex),
                $autofilter->toRow
            );
            $databaseRange = '<table:database-range table:name="__Anonymous_Sheet_DB__'.$externalSheet->getIndex().'" table:target-range-address="'.$rangeAddress.'" table:display-filter-buttons="true"/>';
        }

        return $databaseRange;
    }

    
    public function addRow(Worksheet $worksheet, Row $row): void
    {
        $cells = $row->getCells();
        $rowStyle = $row->getStyle();

        $data = '<table:table-row table:style-name="ro1">';

        $currentCellIndex = 0;
        $nextCellIndex = 1;

        for ($i = 0; $i < $row->getNumCells(); ++$i) {
            
            $cell = $cells[$currentCellIndex];

            
            $nextCell = $cells[$nextCellIndex] ?? null;

            if (null === $nextCell || $cell->getValue() !== $nextCell->getValue()) {
                $registeredStyle = $this->applyStyleAndRegister($cell, $rowStyle);
                $cellStyle = $registeredStyle->getStyle();
                if ($registeredStyle->isMatchingRowStyle()) {
                    $rowStyle = $cellStyle; 
                }

                $data .= $this->getCellXMLWithStyle($cell, $cellStyle, $currentCellIndex, $nextCellIndex);
                $currentCellIndex = $nextCellIndex;
            }

            ++$nextCellIndex;
        }

        $data .= '</table:table-row>';

        $wasWriteSuccessful = fwrite($worksheet->getFilePointer(), $data);
        if (false === $wasWriteSuccessful) {
            throw new IOException("Unable to write data in {$worksheet->getFilePath()}");
        }

        
        $lastWrittenRowIndex = $worksheet->getLastWrittenRowIndex();
        $worksheet->setLastWrittenRowIndex($lastWrittenRowIndex + 1);
    }

    
    public function close(Worksheet $worksheet): void
    {
        fclose($worksheet->getFilePointer());
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

    private function getCellXMLWithStyle(Cell $cell, Style $style, int $currentCellIndex, int $nextCellIndex): string
    {
        $styleIndex = $style->getId() + 1; 

        $numTimesValueRepeated = ($nextCellIndex - $currentCellIndex);

        return $this->getCellXML($cell, $styleIndex, $numTimesValueRepeated);
    }

    
    private function getCellXML(Cell $cell, int $styleIndex, int $numTimesValueRepeated): string
    {
        $data = '<table:table-cell table:style-name="ce'.$styleIndex.'"';

        if (1 !== $numTimesValueRepeated) {
            $data .= ' table:number-columns-repeated="'.$numTimesValueRepeated.'"';
        }

        if ($cell instanceof Cell\StringCell) {
            $data .= ' office:value-type="string" calcext:value-type="string">';

            $cellValueLines = explode("\n", $cell->getValue());
            foreach ($cellValueLines as $cellValueLine) {
                $data .= '<text:p>'.$this->stringsEscaper->escape($cellValueLine).'</text:p>';
            }

            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\BooleanCell) {
            $value = $cell->getValue() ? 'true' : 'false'; 
            $data .= ' office:value-type="boolean" calcext:value-type="boolean" office:boolean-value="'.$value.'">';
            $data .= '<text:p>'.$cell->getValue().'</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\NumericCell) {
            $cellValue = $cell->getValue();
            $data .= ' office:value-type="float" calcext:value-type="float" office:value="'.$cellValue.'">';
            $data .= '<text:p>'.$cellValue.'</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\DateTimeCell) {
            $datevalue = substr((new DateTimeImmutable('@'.$cell->getValue()->getTimestamp()))->format(DateTimeInterface::W3C), 0, -6);
            $data .= ' office:value-type="date" calcext:value-type="date" office:date-value="'.$datevalue.'Z">';
            $data .= '<text:p>'.$datevalue.'Z</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\DateIntervalCell) {
            
            static $f = ['M0S', 'H0M', 'DT0H', 'M0D', 'Y0M', 'P0Y', 'Y0M', 'P0M'];
            static $r = ['M', 'H', 'DT', 'M', 'Y0M', 'P', 'Y', 'P'];
            $value = rtrim(str_replace($f, $r, $cell->getValue()->format('P%yY%mM%dDT%hH%iM%sS')), 'PT') ?: 'PT0S';
            $data .= ' office:value-type="time" office:time-value="'.$value.'">';
            $data .= '<text:p>'.$value.'</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\ErrorCell) {
            
            $data .= ' office:value-type="string" calcext:value-type="error" office:value="">';
            $data .= '<text:p>'.$cell->getRawValue().'</text:p>';
            $data .= '</table:table-cell>';
        } elseif ($cell instanceof Cell\EmptyCell) {
            $data .= '/>';
        }

        return $data;
    }
}
