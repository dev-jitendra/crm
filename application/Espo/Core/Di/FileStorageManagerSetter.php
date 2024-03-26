<?php


namespace Espo\Core\Di;

use Espo\Core\FileStorage\Manager as FileStorageManager;

trait FileStorageManagerSetter
{
    
    protected $fileStorageManager;

    public function setFileStorageManager(FileStorageManager $fileStorageManager): void
    {
        $this->fileStorageManager = $fileStorageManager;
    }
}
