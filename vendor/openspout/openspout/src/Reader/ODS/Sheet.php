<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use OpenSpout\Reader\SheetWithVisibilityInterface;


final class Sheet implements SheetWithVisibilityInterface
{
    
    private readonly RowIterator $rowIterator;

    
    private readonly int $index;

    
    private readonly string $name;

    
    private readonly bool $isActive;

    
    private readonly bool $isVisible;

    
    public function __construct(RowIterator $rowIterator, int $sheetIndex, string $sheetName, bool $isSheetActive, bool $isSheetVisible)
    {
        $this->rowIterator = $rowIterator;
        $this->index = $sheetIndex;
        $this->name = $sheetName;
        $this->isActive = $isSheetActive;
        $this->isVisible = $isSheetVisible;
    }

    public function getRowIterator(): RowIterator
    {
        return $this->rowIterator;
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
