<?php


namespace Espo\Tools\ExportCustom;

class Result
{
    public function __construct(private string $attachmentId) {}

    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }
}
