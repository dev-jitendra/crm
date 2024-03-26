<?php


namespace Espo\Core\Mail\Message\MailMimeParser;

use Espo\Core\Mail\Message\Part as PartInterface;

use ZBateson\MailMimeParser\Message\Part\MessagePart;

class Part implements PartInterface
{
    private MessagePart $part;

    public function __construct(MessagePart $part)
    {
        $this->part = $part;
    }

    public function getContentType(): ?string
    {
        return $this->part->getContentType();
    }

    public function hasContent(): bool
    {
        return $this->part->hasContent();
    }

    public function getContent(): ?string
    {
        return $this->part->getContent();
    }

    public function getContentId(): ?string
    {
        return $this->part->getContentId();
    }

    public function getCharset(): ?string
    {
        return $this->part->getCharset();
    }

    public function getContentDisposition(): ?string
    {
        return $this->part->getContentDisposition();
    }

    public function getFilename(): ?string
    {
        return $this->part->getFilename();
    }
}
