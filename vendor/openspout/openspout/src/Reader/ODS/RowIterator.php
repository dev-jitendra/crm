<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use DOMElement;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\Common\XMLProcessor;
use OpenSpout\Reader\Exception\InvalidValueException;
use OpenSpout\Reader\Exception\IteratorNotRewindableException;
use OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use OpenSpout\Reader\RowIteratorInterface;
use OpenSpout\Reader\Wrapper\XMLReader;

final class RowIterator implements RowIteratorInterface
{
    
    public const XML_NODE_TABLE = 'table:table';
    public const XML_NODE_ROW = 'table:table-row';
    public const XML_NODE_CELL = 'table:table-cell';
    public const MAX_COLUMNS_EXCEL = 16384;

    
    public const XML_ATTRIBUTE_NUM_ROWS_REPEATED = 'table:number-rows-repeated';
    public const XML_ATTRIBUTE_NUM_COLUMNS_REPEATED = 'table:number-columns-repeated';

    private readonly Options $options;

    
    private readonly XMLProcessor $xmlProcessor;

    
    private readonly Helper\CellValueFormatter $cellValueFormatter;

    
    private bool $hasAlreadyBeenRewound = false;

    
    private Row $currentlyProcessedRow;

    
    private ?Row $rowBuffer = null;

    
    private bool $hasReachedEndOfFile = false;

    
    private int $lastRowIndexProcessed = 0;

    
    private int $nextRowIndexToBeProcessed = 1;

    
    private ?Cell $lastProcessedCell = null;

    
    private int $numRowsRepeated = 1;

    
    private int $numColumnsRepeated = 1;

    
    private bool $hasAlreadyReadOneCellInCurrentRow = false;

    public function __construct(
        Options $options,
        CellValueFormatter $cellValueFormatter,
        XMLProcessor $xmlProcessor
    ) {
        $this->cellValueFormatter = $cellValueFormatter;

        
        $this->xmlProcessor = $xmlProcessor;
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_START, [$this, 'processRowStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_CELL, XMLProcessor::NODE_TYPE_START, [$this, 'processCellStartingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_ROW, XMLProcessor::NODE_TYPE_END, [$this, 'processRowEndingNode']);
        $this->xmlProcessor->registerCallback(self::XML_NODE_TABLE, XMLProcessor::NODE_TYPE_END, [$this, 'processTableEndingNode']);
        $this->options = $options;
    }

    
    public function rewind(): void
    {
        
        
        
        if ($this->hasAlreadyBeenRewound) {
            throw new IteratorNotRewindableException();
        }

        $this->hasAlreadyBeenRewound = true;
        $this->lastRowIndexProcessed = 0;
        $this->nextRowIndexToBeProcessed = 1;
        $this->rowBuffer = null;
        $this->hasReachedEndOfFile = false;

        $this->next();
    }

    
    public function valid(): bool
    {
        return !$this->hasReachedEndOfFile;
    }

    
    public function next(): void
    {
        if ($this->doesNeedDataForNextRowToBeProcessed()) {
            $this->readDataForNextRow();
        }

        ++$this->lastRowIndexProcessed;
    }

    
    public function current(): Row
    {
        return $this->rowBuffer;
    }

    
    public function key(): int
    {
        return $this->lastRowIndexProcessed;
    }

    
    private function doesNeedDataForNextRowToBeProcessed(): bool
    {
        $hasReadAtLeastOneRow = (0 !== $this->lastRowIndexProcessed);

        return
            !$hasReadAtLeastOneRow
            || $this->lastRowIndexProcessed === $this->nextRowIndexToBeProcessed - 1;
    }

    
    private function readDataForNextRow(): void
    {
        $this->currentlyProcessedRow = new Row([], null);

        $this->xmlProcessor->readUntilStopped();

        $this->rowBuffer = $this->currentlyProcessedRow;
    }

    
    private function processRowStartingNode(XMLReader $xmlReader): int
    {
        
        $this->hasAlreadyReadOneCellInCurrentRow = false;
        $this->lastProcessedCell = null;
        $this->numColumnsRepeated = 1;
        $this->numRowsRepeated = $this->getNumRowsRepeatedForCurrentNode($xmlReader);

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processCellStartingNode(XMLReader $xmlReader): int
    {
        $currentNumColumnsRepeated = $this->getNumColumnsRepeatedForCurrentNode($xmlReader);

        
        
        $node = $xmlReader->expand();
        $currentCell = $this->getCell($node);

        
        if ($this->hasAlreadyReadOneCellInCurrentRow) {
            for ($i = 0; $i < $this->numColumnsRepeated; ++$i) {
                $this->currentlyProcessedRow->addCell($this->lastProcessedCell);
            }
        }

        $this->hasAlreadyReadOneCellInCurrentRow = true;
        $this->lastProcessedCell = $currentCell;
        $this->numColumnsRepeated = $currentNumColumnsRepeated;

        return XMLProcessor::PROCESSING_CONTINUE;
    }

    
    private function processRowEndingNode(): int
    {
        $isEmptyRow = $this->isEmptyRow($this->currentlyProcessedRow, $this->lastProcessedCell);

        
        if (!$this->options->SHOULD_PRESERVE_EMPTY_ROWS && $isEmptyRow) {
            
            return XMLProcessor::PROCESSING_CONTINUE;
        }

        
        $actualNumColumnsRepeated = (!$isEmptyRow) ? $this->numColumnsRepeated : 1;
        $numCellsInCurrentlyProcessedRow = $this->currentlyProcessedRow->getNumCells();

        
        
        
        
        
        
        if (($numCellsInCurrentlyProcessedRow + $actualNumColumnsRepeated) !== self::MAX_COLUMNS_EXCEL) {
            for ($i = 0; $i < $actualNumColumnsRepeated; ++$i) {
                $this->currentlyProcessedRow->addCell($this->lastProcessedCell);
            }
        }

        
        
        $this->nextRowIndexToBeProcessed += $this->numRowsRepeated;

        
        
        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function processTableEndingNode(): int
    {
        
        $this->hasReachedEndOfFile = true;

        return XMLProcessor::PROCESSING_STOP;
    }

    
    private function getNumRowsRepeatedForCurrentNode(XMLReader $xmlReader): int
    {
        $numRowsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_ROWS_REPEATED);

        return (null !== $numRowsRepeated) ? (int) $numRowsRepeated : 1;
    }

    
    private function getNumColumnsRepeatedForCurrentNode(XMLReader $xmlReader): int
    {
        $numColumnsRepeated = $xmlReader->getAttribute(self::XML_ATTRIBUTE_NUM_COLUMNS_REPEATED);

        return (null !== $numColumnsRepeated) ? (int) $numColumnsRepeated : 1;
    }

    
    private function getCell(DOMElement $node): Cell
    {
        try {
            $cellValue = $this->cellValueFormatter->extractAndFormatNodeValue($node);
            $cell = Cell::fromValue($cellValue);
        } catch (InvalidValueException $exception) {
            $cell = new Cell\ErrorCell($exception->getInvalidValue(), null);
        }

        return $cell;
    }

    
    private function isEmptyRow(Row $currentRow, ?Cell $lastReadCell): bool
    {
        return
            $currentRow->isEmpty()
            && (null === $lastReadCell || $lastReadCell instanceof Cell\EmptyCell);
    }
}
