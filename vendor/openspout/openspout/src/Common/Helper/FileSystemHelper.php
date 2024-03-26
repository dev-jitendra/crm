<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;

use OpenSpout\Common\Exception\IOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


final class FileSystemHelper implements FileSystemHelperInterface
{
    
    private readonly string $baseFolderRealPath;

    
    public function __construct(string $baseFolderPath)
    {
        $realpath = realpath($baseFolderPath);
        \assert(false !== $realpath);
        $this->baseFolderRealPath = $realpath;
    }

    public function getBaseFolderRealPath(): string
    {
        return $this->baseFolderRealPath;
    }

    
    public function createFolder(string $parentFolderPath, string $folderName): string
    {
        $this->throwIfOperationNotInBaseFolder($parentFolderPath);

        $folderPath = $parentFolderPath.\DIRECTORY_SEPARATOR.$folderName;

        $errorMessage = '';
        set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
            $errorMessage = $message;

            return true;
        });
        $wasCreationSuccessful = mkdir($folderPath, 0777, true);
        restore_error_handler();

        if (!$wasCreationSuccessful) {
            throw new IOException("Unable to create folder: {$folderPath} - {$errorMessage}");
        }

        return $folderPath;
    }

    
    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string
    {
        $this->throwIfOperationNotInBaseFolder($parentFolderPath);

        $filePath = $parentFolderPath.\DIRECTORY_SEPARATOR.$fileName;

        $errorMessage = '';
        set_error_handler(static function ($nr, $message) use (&$errorMessage): bool {
            $errorMessage = $message;

            return true;
        });
        $wasCreationSuccessful = file_put_contents($filePath, $fileContents);
        restore_error_handler();

        if (false === $wasCreationSuccessful) {
            throw new IOException("Unable to create file: {$filePath} - {$errorMessage}");
        }

        return $filePath;
    }

    
    public function deleteFile(string $filePath): void
    {
        $this->throwIfOperationNotInBaseFolder($filePath);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    
    public function deleteFolderRecursively(string $folderPath): void
    {
        $this->throwIfOperationNotInBaseFolder($folderPath);

        $itemIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($itemIterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($folderPath);
    }

    
    private function throwIfOperationNotInBaseFolder(string $operationFolderPath): void
    {
        $operationFolderRealPath = realpath($operationFolderPath);
        if (false === $operationFolderRealPath) {
            throw new IOException("Folder not found: {$operationFolderRealPath}");
        }
        $isInBaseFolder = str_starts_with($operationFolderRealPath, $this->baseFolderRealPath);
        if (!$isInBaseFolder) {
            throw new IOException("Cannot perform I/O operation outside of the base folder: {$this->baseFolderRealPath}");
        }
    }
}
