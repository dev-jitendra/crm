<?php


namespace Espo\Tools\Export;


class Result
{
    private string $attachmentId;

    public function __construct(string $attachmentId)
    {
        $this->attachmentId = $attachmentId;
    }

    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }
}
