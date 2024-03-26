<?php

namespace Laminas\Mail\Storage\Writable;

use Laminas\Mail\Message;
use Laminas\Mail\Storage;
use Laminas\Mime;

interface WritableInterface
{
    
    public function createFolder($name, $parentFolder = null);

    
    public function removeFolder($name);

    
    public function renameFolder($oldName, $newName);

    
    public function appendMessage($message, $folder = null, $flags = null);

    
    public function copyMessage($id, $folder);

    
    public function moveMessage($id, $folder);

    
    public function setFlags($id, $flags);
}
