<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\File\Manager as FileManager;

interface FileManagerAware
{
    public function setFileManager(FileManager $fileManager): void;
}
