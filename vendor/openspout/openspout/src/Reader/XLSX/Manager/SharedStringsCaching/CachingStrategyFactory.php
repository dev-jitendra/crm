<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;


final class CachingStrategyFactory implements CachingStrategyFactoryInterface
{
    
    public const AMOUNT_MEMORY_NEEDED_PER_STRING_IN_KB = 12;

    
    public const MAX_NUM_STRINGS_PER_TEMP_FILE = 10000;

    private readonly MemoryLimit $memoryLimit;

    public function __construct(MemoryLimit $memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }

    
    public function createBestCachingStrategy(?int $sharedStringsUniqueCount, string $tempFolder): CachingStrategyInterface
    {
        if ($this->isInMemoryStrategyUsageSafe($sharedStringsUniqueCount)) {
            return new InMemoryStrategy($sharedStringsUniqueCount);
        }

        return new FileBasedStrategy($tempFolder, self::MAX_NUM_STRINGS_PER_TEMP_FILE);
    }

    
    private function isInMemoryStrategyUsageSafe(?int $sharedStringsUniqueCount): bool
    {
        
        if (null === $sharedStringsUniqueCount) {
            return false;
        }

        $memoryAvailable = $this->memoryLimit->getMemoryLimitInKB();

        if (-1 === (int) $memoryAvailable) {
            
            $isInMemoryStrategyUsageSafe = ($sharedStringsUniqueCount < self::MAX_NUM_STRINGS_PER_TEMP_FILE);
        } else {
            $memoryNeeded = $sharedStringsUniqueCount * self::AMOUNT_MEMORY_NEEDED_PER_STRING_IN_KB;
            $isInMemoryStrategyUsageSafe = ($memoryAvailable > $memoryNeeded);
        }

        return $isInMemoryStrategyUsageSafe;
    }
}
