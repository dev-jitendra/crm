<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

use OpenSpout\Common\Entity\Row;

interface WriterInterface
{
    
    public function openToFile(string $outputFilePath): void;

    
    public function openToBrowser(string $outputFileName): void;

    
    public function addRow(Row $row): void;

    
    public function addRows(array $rows): void;

    
    public function setCreator(string $creator): void;

    
    public function getWrittenRowCount(): int;

    
    public function close(): void;
}
