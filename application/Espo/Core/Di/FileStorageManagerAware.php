<?php


namespace Espo\Core\Di;

use Espo\Core\FileStorage\Manager as FileStorageManager;

interface FileStorageManagerAware
{
    public function setFileStorageManager(FileStorageManager $fileStorageManager): void;
}
