<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;


final class ZipHelper
{
    public const ZIP_EXTENSION = '.zip';

    
    public const EXISTING_FILES_SKIP = 'skip';
    public const EXISTING_FILES_OVERWRITE = 'overwrite';

    
    public function createZip(string $tmpFolderPath): ZipArchive
    {
        $zip = new ZipArchive();
        $zipFilePath = $tmpFolderPath.self::ZIP_EXTENSION;

        $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        return $zip;
    }

    
    public function getZipFilePath(ZipArchive $zip): string
    {
        return $zip->filename;
    }

    
    public function addFileToArchive(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $this->addFileToArchiveWithCompressionMethod(
            $zip,
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            ZipArchive::CM_DEFAULT
        );
    }

    
    public function addUncompressedFileToArchive(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $this->addFileToArchiveWithCompressionMethod(
            $zip,
            $rootFolderPath,
            $localFilePath,
            $existingFileMode,
            ZipArchive::CM_STORE
        );
    }

    
    public function addFolderToArchive(ZipArchive $zip, string $folderPath, string $existingFileMode = self::EXISTING_FILES_OVERWRITE): void
    {
        $folderRealPath = $this->getNormalizedRealPath($folderPath).'/';
        $itemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($itemIterator as $itemInfo) {
            \assert($itemInfo instanceof SplFileInfo);
            $itemRealPath = $this->getNormalizedRealPath($itemInfo->getPathname());
            $itemLocalPath = str_replace($folderRealPath, '', $itemRealPath);

            if ($itemInfo->isFile() && !$this->shouldSkipFile($zip, $itemLocalPath, $existingFileMode)) {
                $zip->addFile($itemRealPath, $itemLocalPath);
            }
        }
    }

    
    public function closeArchiveAndCopyToStream(ZipArchive $zip, $streamPointer): void
    {
        $zipFilePath = $zip->filename;
        $zip->close();

        $this->copyZipToStream($zipFilePath, $streamPointer);
    }

    
    private function addFileToArchiveWithCompressionMethod(ZipArchive $zip, string $rootFolderPath, string $localFilePath, string $existingFileMode, int $compressionMethod): void
    {
        $normalizedLocalFilePath = str_replace('\\', '/', $localFilePath);
        if (!$this->shouldSkipFile($zip, $normalizedLocalFilePath, $existingFileMode)) {
            $normalizedFullFilePath = $this->getNormalizedRealPath($rootFolderPath.'/'.$normalizedLocalFilePath);
            $zip->addFile($normalizedFullFilePath, $normalizedLocalFilePath);

            $zip->setCompressionName($normalizedLocalFilePath, $compressionMethod);
        }
    }

    
    private function shouldSkipFile(ZipArchive $zip, string $itemLocalPath, string $existingFileMode): bool
    {
        
        
        
        return self::EXISTING_FILES_SKIP === $existingFileMode && false !== $zip->locateName($itemLocalPath);
    }

    
    private function getNormalizedRealPath(string $path): string
    {
        $realPath = realpath($path);
        \assert(false !== $realPath);

        return str_replace(\DIRECTORY_SEPARATOR, '/', $realPath);
    }

    
    private function copyZipToStream(string $zipFilePath, $pointer): void
    {
        $zipFilePointer = fopen($zipFilePath, 'r');
        \assert(false !== $zipFilePointer);
        stream_copy_to_stream($zipFilePointer, $pointer);
        fclose($zipFilePointer);
    }
}
