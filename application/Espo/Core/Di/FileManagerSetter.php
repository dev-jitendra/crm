<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\File\Manager as FileManager;

trait FileManagerSetter
{
    
    protected $fileManager;

    public function setFileManager(FileManager $fileManager): void
    {
        $this->fileManager = $fileManager;
    }
}
