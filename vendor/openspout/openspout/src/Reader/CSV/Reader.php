<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Common\Helper\EncodingHelper;
use OpenSpout\Reader\AbstractReader;


final class Reader extends AbstractReader
{
    
    private $filePointer;

    
    private SheetIterator $sheetIterator;

    private readonly Options $options;
    private readonly EncodingHelper $encodingHelper;

    public function __construct(
        ?Options $options = null,
        ?EncodingHelper $encodingHelper = null
    ) {
        $this->options = $options ?? new Options();
        $this->encodingHelper = $encodingHelper ?? EncodingHelper::factory();
    }

    public function getSheetIterator(): SheetIterator
    {
        $this->ensureStreamOpened();

        return $this->sheetIterator;
    }

    
    protected function doesSupportStreamWrapper(): bool
    {
        return true;
    }

    
    protected function openReader(string $filePath): void
    {
        $resource = fopen($filePath, 'r');
        \assert(false !== $resource);
        $this->filePointer = $resource;

        $this->sheetIterator = new SheetIterator(
            new Sheet(
                new RowIterator(
                    $this->filePointer,
                    $this->options,
                    $this->encodingHelper
                )
            )
        );
    }

    
    protected function closeReader(): void
    {
        fclose($this->filePointer);
    }
}
