<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager\SharedStringsCaching;

use OpenSpout\Common\Helper\FileSystemHelper;
use OpenSpout\Reader\Exception\SharedStringNotFoundException;


final class FileBasedStrategy implements CachingStrategyInterface
{
    
    public const ESCAPED_LINE_FEED_CHARACTER = '_x000A_';

    
    private readonly FileSystemHelper $fileSystemHelper;

    
    private readonly string $tempFolder;

    
    private readonly int $maxNumStringsPerTempFile;

    
    private $tempFilePointer;

    
    private string $inMemoryTempFilePath = '';

    
    private array $inMemoryTempFileContents;

    
    public function __construct(string $tempFolder, int $maxNumStringsPerTempFile)
    {
        $this->fileSystemHelper = new FileSystemHelper($tempFolder);
        $this->tempFolder = $this->fileSystemHelper->createFolder($tempFolder, uniqid('sharedstrings'));

        $this->maxNumStringsPerTempFile = $maxNumStringsPerTempFile;
    }

    
    public function addStringForIndex(string $sharedString, int $sharedStringIndex): void
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);

        if (!file_exists($tempFilePath)) {
            if (null !== $this->tempFilePointer) {
                fclose($this->tempFilePointer);
            }
            $resource = fopen($tempFilePath, 'w');
            \assert(false !== $resource);
            $this->tempFilePointer = $resource;
        }

        
        
        $lineFeedEncodedSharedString = $this->escapeLineFeed($sharedString);

        fwrite($this->tempFilePointer, $lineFeedEncodedSharedString.PHP_EOL);
    }

    
    public function closeCache(): void
    {
        
        if (null !== $this->tempFilePointer) {
            fclose($this->tempFilePointer);
        }
    }

    
    public function getStringAtIndex(int $sharedStringIndex): string
    {
        $tempFilePath = $this->getSharedStringTempFilePath($sharedStringIndex);
        $indexInFile = $sharedStringIndex % $this->maxNumStringsPerTempFile;

        if (!file_exists($tempFilePath)) {
            throw new SharedStringNotFoundException("Shared string temp file not found: {$tempFilePath} ; for index: {$sharedStringIndex}");
        }

        if ($this->inMemoryTempFilePath !== $tempFilePath) {
            $tempFilePath = realpath($tempFilePath);
            \assert(false !== $tempFilePath);
            $contents = file_get_contents($tempFilePath);
            \assert(false !== $contents);
            $this->inMemoryTempFileContents = explode(PHP_EOL, $contents);
            $this->inMemoryTempFilePath = $tempFilePath;
        }

        $sharedString = null;

        
        if (isset($this->inMemoryTempFileContents[$indexInFile])) {
            $escapedSharedString = $this->inMemoryTempFileContents[$indexInFile];
            $sharedString = $this->unescapeLineFeed($escapedSharedString);
        }

        if (null === $sharedString) {
            throw new SharedStringNotFoundException("Shared string not found for index: {$sharedStringIndex}");
        }

        return rtrim($sharedString, PHP_EOL);
    }

    
    public function clearCache(): void
    {
        $this->fileSystemHelper->deleteFolderRecursively($this->tempFolder);
    }

    
    private function getSharedStringTempFilePath(int $sharedStringIndex): string
    {
        $numTempFile = (int) ($sharedStringIndex / $this->maxNumStringsPerTempFile);

        return $this->tempFolder.\DIRECTORY_SEPARATOR.'sharedstrings'.$numTempFile;
    }

    
    private function escapeLineFeed(string $unescapedString): string
    {
        return str_replace("\n", self::ESCAPED_LINE_FEED_CHARACTER, $unescapedString);
    }

    
    private function unescapeLineFeed(string $escapedString): string
    {
        return str_replace(self::ESCAPED_LINE_FEED_CHARACTER, "\n", $escapedString);
    }
}
