<?php


namespace Espo\Core\Mail\Message;

interface Part
{
    public function getContentType(): ?string;

    public function hasContent(): bool;

    public function getContent(): ?string;

    public function getContentId(): ?string;

    public function getCharset(): ?string;

    public function getContentDisposition(): ?string;

    public function getFilename(): ?string;
}
