<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use DOMElement;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Common\Manager\RowManager;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\RowIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;
use OpenSpout\Reader\XLSX\Helper\CellHelper;
use OpenSpout\Reader\XLSX\Helper\CellValueFormatter;

final class RowIterator implements RowIteratorInterface
{
    
    public const XML_NODE_DIMENSION = 'dimension';
    public const XML_NODE_WORKSHEET = 'worksheet';
    public const XML_NODE_ROW = 'row';
    public const XML_NODE_CELL = 'c';

    
    public const XML_ATTRIBUTE_REF = 'ref';
    public const XML_ATTRIBUTE_SPANS = 'spans';
    public const XML_ATTRIBUTE_ROW_INDEX = 'r';
    public const XML_ATTRIBUTE_CELL_INDEX = 'r';

    
    private readonly string $filePath;

    
    private readonly string $sheetDataXMLFilePath;

    
    private readonly XMLReader $xmlReader;

    
    private readonly XMLProcessor $xmlProcessor;

    
    private readonly Helper\CellValueFormatter $cellValueFormatter;

    
    private readonly RowManager $rowManager;

    
    private int $numReadRows = 0;

    
    private Row $currentlyProcessedRow;

    
    private ?Row $rowBuffer = null;

    
    private bool $hasReachedEndOfFile = false;

    
    private int $numColumns = 0;

    
    private readonly bool $shouldPreserveEmptyRows;

    
    private int $lastRowIndexProcessed = 0;

    
    private int $nextRowIndexToBeProcessed = 0;

    
    private int $lastColumnIndexProcessed = -1;

    
    public function __construct(
        string $filePath,
        string $sheetDataXMLFilePath,
        bool $shouldPreserveEmptyRows,
        XMLReader $xmlReader,
        XMLProcessor $xmlProcessor,
        CellValueFormatter $cellValueFormatter,
        RowManager $rowManager
    ) {
        $this->filePath = $filePath;
        $this->sheetDataXMLFilePath = $this->normalizeSheetDataXMLFilePath($sheetDataXMLFilePath);
        $this->shouldPreserveEmptyRows = $shouldPreserveEmptyRows;
        $this->xmlReader = $xmlReader;
        $this->cellValueFormatter = $cellValueFormatter;
        $this->rowManager = $rowManager;

        
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_DIMENSION, XMLProcessor::NODE_TYPE_START, [$this, 'processDimensionStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_WORKSHEET, XMLProcessor::NODE_TYPE_END, [$this, 'processWorksheetEndingNode']);
    }

    
    public function rewind(): void
    {
        $this->xmlReader->close();

        if (false === $this->xmlReader->openFileInZip($this->filePath, $this->sheetDataXMLFilePath)) {
            throw new IOException("Could not open \"{$this->sheetDataXMLFilePath}\".");
        }

        $this->numReadRows = 0;
        $this->lastRowIndexProcessed = 0;
        $this->nextRowIndexToBeProcessed = 0;
        $this->rowBuffer = null;
        $this->hasReachedEndOfFile = false;
        $this->numColumns = 0;

        $this->next();
    }

    
    public function valid(): bool
    {
        $valid = !$this->hasReachedEndOfFile;
        if (!$valid) {
            $this->xmlReader->close();
        }

        return $valid;
    }

    
    public function next(): void
    {
        ++$this->nextRowIndexToBeProcessed;

        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }
    }

    
    public function current(): Row
    {
        $rowToBeProcessed = $this->rowBuffer;

        if ($this->shouldPreserveEmptyRows) {
            
            
            
            
            if ($this->lastRowIndexProcessed !== $this->nextRowIndexToBeProcessed) {
                
                
                $rowToBeProcessed = new Row([], null);
            }
        }

        \assert(null !== $rowToBeProcessed);

        return $rowToBeProcessed;
    }

    
    public function key(): int
    {
        
        
        
        return $this->shouldPreserveEmptyRows ?
                $this->nextRowIndexToBeProcessed :
                $this->numReadRows;
    }

    
    private function normalizeSheetDataXMLFilePath(string $sheetDataXMLFilePath): string
    {
        return ltrim($sheetDataXMLFilePath, '/');
    }

    
    private function doesNeedDataForNextRowToBeProcessed(): bool
    {
        $hasReadAtLeastOneRow = (0 !== $this->lastRowIndexProcessed);

        return
            !$hasReadAtLeastOneRow
            || !$this->shouldPreserveEmptyRows
            || $this->lastRowIndexProcessed < $this->nextRowIndexToBeProcessed;
    }

    
    private function readDataForNextRow(): void
    {
        $this->currentlyProcessedRow = new Row([], null);

        $this->xmlProcessor->readUntilStopped();

        $this->rowBuffer = $this->currentlyProcessedRow;
    }

    
    private function processDimensionStartingNode(XMLReader $xmlReader): int
    {
        
        $dimensionRef = $xmlReader->getAttribute(self::XML_ATTRIBUTE_REF); 
        \assert(null !== $dimensionRef);
        if (1 === preg_match('/[A-Z]+\d+:([A-Z]+\d+)/', $dimensionRef, $matches)) {
            $this->numColumns = CellHelper::getColumnIndexFromCellIndex($matches[1]) + 1;
        }

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processRowStartingNode(XMLReader $xmlReader): int
    {
        
        $this->lastColumnIndexProcessed = -1;

        
        $this->lastRowIndexProcessed = $this->getRowIndex($xmlReader);

        
        $numberOfColumnsForRow = $this->numColumns;
        $spans = $xmlReader->getAttribute(self::XML_ATTRIBUTE_SPANS); 
        if (null !== $spans && '' !== $spans) {
            [, $numberOfColumnsForRow] = explode(':', $spans);
            $numberOfColumnsForRow = (int) $numberOfColumnsForRow;
        }

        $cells = array_fill(0, $numberOfColumnsForRow, Cell::fromValue(''));
        $this->currentlyProcessedRow->setCells($cells);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processCellStartingNode(XMLReader $xmlReader): int
    {
        $currentColumnIndex = $this->getColumnIndex($xmlReader);

        
        $node = $xmlReader->expand();
        \assert($node instanceof DOMElement);
        $cell = $this->cellValueFormatter->extractAndFormatNodeValue($node);

        $this->currentlyProcessedRow->setCellAtIndex($cell, $currentColumnIndex);
        $this->lastColumnIndexProcessed = $currentColumnIndex;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processRowEndingNode(): int
    {
        
        if (!$this->shouldPreserveEmptyRows && $this->currentlyProcessedRow->isEmpty()) {
            
            return XMLProcessor::PROCESSING_CONTINUE;
        }

        ++$this->numReadRows;

        
        if (0 === $this->numColumns) {
            $this->rowManager->fillMissingIndexesWithEmptyCells($this->currentlyProcessedRow);
        }

        
        
        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function processWorksheetEndingNode(): int
    {
        
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function getRowIndex(XMLReader $xmlReader): int
    {
        
        $currentRowIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_ROW_INDEX);

        return (null !== $currentRowIndex) ?
                (int) $currentRowIndex :
                $this->lastRowIndexProcessed + 1;
    }

    
    private function getColumnIndex(XMLReader $xmlReader): int
    {
        
        $currentCellIndex = $xmlReader->getAttribute(self::XML_ATTRIBUTE_CELL_INDEX);

        return (null !== $currentCellIndex) ?
                CellHelper::getColumnIndexFromCellIndex($currentCellIndex) :
                $this->lastColumnIndexProcessed + 1;
    }
}
