<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Reader\Common\ColumnWidth;
use OpenSpout\Reader\SheetWithVisibilityInterface;


final class Sheet implements SheetWithVisibilityInterface
{
    
    private readonly RowIterator $rowIterator;

    
    private readonly SheetHeaderReader $headerReader;

    
    private readonly int $index;

    
    private readonly string $name;

    
    private readonly bool $isActive;

    
    private readonly bool $isVisible;

    
    public function __construct(RowIterator $rowIterator, SheetHeaderReader $headerReader, int $sheetIndex, string $sheetName, bool $isSheetActive, bool $isSheetVisible)
    {
        $this->rowIterator = $rowIterator;
        $this->headerReader = $headerReader;
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
        $this->isVisible = $isSheetVisible;
    }

    public function getRowIterator(): RowIterator
    {
        return $this->rowIterator;
    }

    
    public function getColumnWidths(): array
    {
        return $this->headerReader->getColumnWidths();
    }

    
    public function getIndex(): int
    {
        return $this->index;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function isActive(): bool
    {
        return $this->isActive;
    }

    
    public function isVisible(): bool
    {
        return $this->isVisible;
    }
}
