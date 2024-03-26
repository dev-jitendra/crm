<?php

declare(strict_types=1);

namespace OpenSpout\Reader;


interface ReaderInterface
{
    
    public function open(string $filePath): void;

    
    public function getSheetIterator(): SheetIteratorInterface;

    
    public function close(): void;
}
