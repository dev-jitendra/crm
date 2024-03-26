<?php

declare(strict_types=1);

namespace OpenSpout\Reader;


interface SheetInterface
{
    
    public function getRowIterator(): RowIteratorInterface;

    
    public function getIndex(): int;

    
    public function getName(): string;

    
    public function isActive(): bool;
}
