<?php


namespace Espo\Core\FileStorage;

use Psr\Http\Message\StreamInterface;


interface Storage
{
    
    public function getStream(Attachment $attachment): StreamInterface;

    
    public function putStream(Attachment $attachment, StreamInterface $stream): void;

    
    public function exists(Attachment $attachment): bool;

    
    public function unlink(Attachment $attachment): void;

    
    public function getSize(Attachment $attachment): int;
}
