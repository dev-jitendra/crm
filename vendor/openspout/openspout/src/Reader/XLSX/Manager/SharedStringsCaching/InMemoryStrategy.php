<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Reader\Exception\SharedStringNotFoundException;
use RuntimeException;
use SplFixedArray;


final class InMemoryStrategy implements CachingStrategyInterface
{
    
    private SplFixedArray $inMemoryCache;

    
    private bool $isCacheClosed = false;

    
    public function __construct(int $sharedStringsUniqueCount)
    {
        $this->inMemoryCache = new SplFixedArray($sharedStringsUniqueCount);
    }

    
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void
    {
        if (!$this->isCacheClosed) {
            $this->inMemoryCache->offsetSet($sharedStringIndex, $sharedString);
        }
    }

    
    public function closeCache(): void
    {
        $this->isCacheClosed = true;
    }

    
    public function getStringAtIndex(int $sharedStringIndex): string
    {
        try {
            return $this->inMemoryCache->offsetGet($sharedStringIndex);
        } catch (RuntimeException) {
            throw new SharedStringNotFoundException("Shared string not found for index: {$sharedStringIndex}");
        }
    }

    
    public function clearCache(): void
    {
        $this->inMemoryCache = new SplFixedArray(0);
        $this->isCacheClosed = false;
    }
}
