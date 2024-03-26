<?php

namespace Laminas\Mail\Storage\Folder;

use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Folder;

interface FolderInterface
{
    
    public function getFolders($rootFolder = null);

    
    public function selectFolder($globalName);

    
    public function getCurrentFolder();
}
