<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;

use OpenSpout\Common\Helper\FileSystemHelperInterface;


interface FileSystemWithRootFolderHelperInterface extends FileSystemHelperInterface
{
    
    public function createBaseFilesAndFolders(): void;

    public function getRootFolder(): string;

    public function getSheetsContentTempFolder(): string;
}
