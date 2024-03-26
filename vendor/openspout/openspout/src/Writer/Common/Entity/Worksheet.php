<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Entity;


final class Worksheet
{
    
    private readonly string $filePath;

    
    private $filePointer;

    
    private readonly Sheet $externalSheet;

    
    private int $maxNumColumns = 0;

    
    private int $lastWrittenRowIndex = 0;

    
    public function __construct(string $worksheetFilePath, Sheet $externalSheet)
    {
        $this->filePath = $worksheetFilePath;
        $this->externalSheet = $externalSheet;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    
    public function getFilePointer()
    {
        \assert(null !== $this->filePointer);

        return $this->filePointer;
    }

    
    public function setFilePointer($filePointer): void
    {
        $this->filePointer = $filePointer;
    }

    public function getExternalSheet(): Sheet
    {
        return $this->externalSheet;
    }

    public function getMaxNumColumns(): int
    {
        return $this->maxNumColumns;
    }

    public function setMaxNumColumns(int $maxNumColumns): void
    {
        $this->maxNumColumns = $maxNumColumns;
    }

    public function getLastWrittenRowIndex(): int
    {
        return $this->lastWrittenRowIndex;
    }

    public function setLastWrittenRowIndex(int $lastWrittenRowIndex): void
    {
        $this->lastWrittenRowIndex = $lastWrittenRowIndex;
    }

    
    public function getId(): int
    {
        
        return $this->externalSheet->getIndex() + 1;
    }
}
