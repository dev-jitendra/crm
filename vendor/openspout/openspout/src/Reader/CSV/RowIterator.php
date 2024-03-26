<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Helper\EncodingHelper;
use OpenSpout\Reader\RowIteratorInterface;


final class RowIterator implements RowIteratorInterface
{
    
    public const MAX_READ_BYTES_PER_LINE = 0;

    
    private $filePointer;

    
    private int $numReadRows = 0;

    
    private ?Row $rowBuffer = null;

    
    private bool $hasReachedEndOfFile = false;

    private readonly Options $options;

    
    private readonly EncodingHelper $encodingHelper;

    
    public function __construct(
        $filePointer,
        Options $options,
        EncodingHelper $encodingHelper
    ) {
        $this->filePointer = $filePointer;
        $this->options = $options;
        $this->encodingHelper = $encodingHelper;
    }

    
    public function rewind(): void
    {
        $this->rewindAndSkipBom();

        $this->numReadRows = 0;
        $this->rowBuffer = null;

        $this->next();
    }

    
    public function valid(): bool
    {
        return null !== $this->filePointer && !$this->hasReachedEndOfFile;
    }

    
    public function next(): void
    {
        $this->hasReachedEndOfFile = feof($this->filePointer);

        if (!$this->hasReachedEndOfFile) {
            $this->readDataForNextRow();
        }
    }

    
    public function current(): ?Row
    {
        return $this->rowBuffer;
    }

    
    public function key(): int
    {
        return $this->numReadRows;
    }

    
    private function rewindAndSkipBom(): void
    {
        $byteOffsetToSkipBom = $this->encodingHelper->getBytesOffsetToSkipBOM($this->filePointer, $this->options->ENCODING);

        
        fseek($this->filePointer, $byteOffsetToSkipBom);
    }

    
    private function readDataForNextRow(): void
    {
        do {
            $rowData = $this->getNextUTF8EncodedRow();
        } while ($this->shouldReadNextRow($rowData));

        if (false !== $rowData) {
            
            $rowDataBufferAsArray = array_map('\\strval', $rowData);
            $this->rowBuffer = new Row(array_map(static function ($cellValue) {
                return Cell::fromValue($cellValue);
            }, $rowDataBufferAsArray), null);
            ++$this->numReadRows;
        } else {
            
            
            $this->hasReachedEndOfFile = true;
        }
    }

    
    private function shouldReadNextRow($currentRowData): bool
    {
        $hasSuccessfullyFetchedRowData = (false !== $currentRowData);
        $hasNowReachedEndOfFile = feof($this->filePointer);
        $isEmptyLine = $this->isEmptyLine($currentRowData);

        return
            (!$hasSuccessfullyFetchedRowData && !$hasNowReachedEndOfFile)
            || (!$this->options->SHOULD_PRESERVE_EMPTY_ROWS && $isEmptyLine);
    }

    
    private function getNextUTF8EncodedRow(): array|false
    {
        $encodedRowData = fgetcsv(
            $this->filePointer,
            self::MAX_READ_BYTES_PER_LINE,
            $this->options->FIELD_DELIMITER,
            $this->options->FIELD_ENCLOSURE,
            ''
        );
        if (false === $encodedRowData) {
            return false;
        }

        foreach ($encodedRowData as $cellIndex => $cellValue) {
            switch ($this->options->ENCODING) {
                case EncodingHelper::ENCODING_UTF16_LE:
                case EncodingHelper::ENCODING_UTF32_LE:
                    
                    $cellValue = ltrim($cellValue);

                    break;

                case EncodingHelper::ENCODING_UTF16_BE:
                case EncodingHelper::ENCODING_UTF32_BE:
                    
                    $cellValue = rtrim($cellValue);

                    break;
            }

            $encodedRowData[$cellIndex] = $this->encodingHelper->attemptConversionToUTF8($cellValue, $this->options->ENCODING);
        }

        return $encodedRowData;
    }

    
    private function isEmptyLine($lineData): bool
    {
        return \is_array($lineData) && 1 === \count($lineData) && null === $lineData[0];
    }
}
