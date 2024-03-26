<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Reader\AbstractReader;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactory;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactoryInterface;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\MemoryLimit;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\SheetManager;
use OpenSpout\Reader\XLSX\Manager\WorkbookRelationshipsManager;
use ZipArchive;


final class Reader extends AbstractReader
{
    private ZipArchive $zip;

    
    private SharedStringsManager $sharedStringsManager;

    
    private SheetIterator $sheetIterator;

    private readonly Options $options;
    private readonly CachingStrategyFactoryInterface $cachingStrategyFactory;

    public function __construct(
        ?Options $options = null,
        ?CachingStrategyFactoryInterface $cachingStrategyFactory = null
    ) {
        $this->options = $options ?? new Options();

        if (null === $cachingStrategyFactory) {
            $memoryLimit = \ini_get('memory_limit');
            $cachingStrategyFactory = new CachingStrategyFactory(new MemoryLimit($memoryLimit));
        }
        $this->cachingStrategyFactory = $cachingStrategyFactory;
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

        $this->sharedStringsManager = new SharedStringsManager(
            $filePath,
            $this->options,
            new WorkbookRelationshipsManager($filePath),
            $this->cachingStrategyFactory
        );

        if ($this->sharedStringsManager->hasSharedStrings()) {
            
            $this->sharedStringsManager->extractSharedStrings();
        }

        $this->sheetIterator = new SheetIterator(
            new SheetManager(
                $filePath,
                $this->options,
                $this->sharedStringsManager,
                new XLSX()
            )
        );
    }

    
    protected function closeReader(): void
    {
        $this->zip->close();
        $this->sharedStringsManager->cleanup();
    }
}
