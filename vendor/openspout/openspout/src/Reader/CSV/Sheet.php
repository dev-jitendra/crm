<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Reader\SheetInterface;


final class Sheet implements SheetInterface
{
    
    private readonly RowIterator $rowIterator;

    
    public function __construct(RowIterator $rowIterator)
    {
        $this->rowIterator = $rowIterator;
    }

    public function getRowIterator(): RowIterator
    {
        return $this->rowIterator;
    }

    
    public function getIndex(): int
    {
        return 0;
    }

    
    public function getName(): string
    {
        return '';
    }

    
    public function isActive(): bool
    {
        return true;
    }
}
