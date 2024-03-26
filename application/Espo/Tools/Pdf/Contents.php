<?php


namespace Espo\Tools\Pdf;

use Psr\Http\Message\StreamInterface;

interface Contents
{
    public function getStream(): StreamInterface;

    public function getString(): string;

    public function getLength(): int;
}
