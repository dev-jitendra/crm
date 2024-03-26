<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;


interface CachingStrategyInterface
{
    
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void;

    
    public function closeCache(): void;

    
    public function getStringAtIndex(int $sharedStringIndex): string;

    
    public function clearCache(): void;
}
