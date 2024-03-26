<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Reader\AbstractReader;
use OpenSpout\Reader\ODS\Helper\SettingsHelper;
use ZipArchive;


final class Reader extends AbstractReader
{
    private ZipArchive $zip;

    private readonly Options $options;

    
    private SheetIterator $sheetIterator;

    public function __construct(?Options $options = null)
    {
        $this->options = $options ?? new Options();
    }

    public function getSheetIterator(): SheetIterator
    {
        $this->ensureStreamOpened();

        return $this->sheetIterator;
    }

    
    protected function doesSupportStreamWrapper(): bool
    {
        return false;
    }

    
    protected function openReader(string $filePath): void
    {
        $this->zip = new ZipArchive();

        if (true !== $this->zip->open($filePath)) {
            throw new IOException("Could not open {$filePath} for reading.");
        }

        $this->sheetIterator = new SheetIterator($filePath, $this->options, new ODS(), new SettingsHelper());
    }

    
    protected function closeReader(): void
    {
        $this->zip->close();
    }
}
