<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Reader\Exception\NoSheetsFoundException;
use OpenSpout\Reader\SheetIteratorInterface;
use OpenSpout\Reader\XLSX\Manager\SheetManager;


final class SheetIterator implements SheetIteratorInterface
{
    
    private array $sheets;

    
    private int $currentSheetIndex = 0;

    
    public function __construct(SheetManager $sheetManager)
    {
        
        $this->sheets = $sheetManager->getSheets();

        if (0 === \count($this->sheets)) {
            throw new NoSheetsFoundException('The file must contain at least one sheet.');
        }
    }

    
    public function rewind(): void
    {
        $this->currentSheetIndex = 0;
    }

    
    public function valid(): bool
    {
        return $this->currentSheetIndex < \count($this->sheets);
    }

    
    public function next(): void
    {
        ++$this->currentSheetIndex;
    }

    
    public function current(): Sheet
    {
        return $this->sheets[$this->currentSheetIndex];
    }

    
    public function key(): int
    {
        return $this->currentSheetIndex + 1;
    }
}
