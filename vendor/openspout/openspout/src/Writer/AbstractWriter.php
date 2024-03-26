<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;

abstract class AbstractWriter implements WriterInterface
{
    
    protected $filePointer;

    
    protected string $creator = 'OpenSpout';

    
    protected static string $headerContentType;

    
    private string $outputFilePath;

    
    private bool $isWriterOpened = false;

    
    private int $writtenRowCount = 0;

    final public function openToFile($outputFilePath): void
    {
        $this->outputFilePath = $outputFilePath;

        $errorMessage = null;
        set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
            $errorMessage = $message;

            return true;
        });

        $resource = fopen($this->outputFilePath, 'w');
        restore_error_handler();
        if (null !== $errorMessage) {
            throw new IOException("Unable to open file {$this->outputFilePath}: {$errorMessage}");
        }
        \assert(false !== $resource);
        $this->filePointer = $resource;

        $this->openWriter();
        $this->isWriterOpened = true;
    }

    
    final public function openToBrowser($outputFileName): void
    {
        $this->outputFilePath = basename($outputFileName);

        $resource = fopen('php:
        \assert(false !== $resource);
        $this->filePointer = $resource;

        
        
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        
        header('Content-Type: '.static::$headerContentType);
        header(
            'Content-Disposition: attachment; '.
            'filename="'.rawurlencode($this->outputFilePath).'"; '.
            'filename*=UTF-8\'\''.rawurlencode($this->outputFilePath)
        );

        
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $this->openWriter();
        $this->isWriterOpened = true;
    }

    final public function addRow(Row $row): void
    {
        if (!$this->isWriterOpened) {
            throw new WriterNotOpenedException('The writer needs to be opened before adding row.');
        }

        $this->addRowToWriter($row);
        ++$this->writtenRowCount;
    }

    final public function addRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    final public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    final public function getWrittenRowCount(): int
    {
        return $this->writtenRowCount;
    }

    final public function close(): void
    {
        if (!$this->isWriterOpened) {
            return;
        }

        $this->closeWriter();

        fclose($this->filePointer);

        $this->isWriterOpened = false;
    }

    
    abstract protected function openWriter(): void;

    
    abstract protected function addRowToWriter(Row $row): void;

    
    abstract protected function closeWriter(): void;
}
