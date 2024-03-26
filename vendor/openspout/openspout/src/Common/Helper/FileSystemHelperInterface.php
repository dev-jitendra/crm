<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;


interface FileSystemHelperInterface
{
    
    public function createFolder(string $parentFolderPath, string $folderName): string;

    
    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string;

    
    public function deleteFile(string $filePath): void;

    
    public function deleteFolderRecursively(string $folderPath): void;
}
