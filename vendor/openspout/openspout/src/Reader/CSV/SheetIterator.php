<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Reader\SheetIteratorInterface;


final class SheetIterator implements SheetIteratorInterface
{
    
    private readonly Sheet $sheet;

    
    private bool $hasReadUniqueSheet = false;

    
    public function __construct(Sheet $sheet)
    {
        $this->sheet = $sheet;
    }

    
    public function rewind(): void
    {
        $this->hasReadUniqueSheet = false;
    }

    
    public function valid(): bool
    {
        return !$this->hasReadUniqueSheet;
    }

    
    public function next(): void
    {
        $this->hasReadUniqueSheet = true;
    }

    
    public function current(): Sheet
    {
        return $this->sheet;
    }

    
    public function key(): int
    {
        return 1;
    }
}
