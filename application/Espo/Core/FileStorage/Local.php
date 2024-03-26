<?php


namespace Espo\Core\FileStorage;


interface Local
{
    
    public function getLocalFilePath(Attachment $attachment): string;
}
