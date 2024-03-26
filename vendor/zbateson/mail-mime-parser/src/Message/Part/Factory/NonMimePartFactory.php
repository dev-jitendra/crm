<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\Part\NonMimePart;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;


class NonMimePartFactory extends MessagePartFactory
{
    
    public function newInstance(PartBuilder $partBuilder, StreamInterface $messageStream = null)
    {
        $partStream = null;
        $contentStream = null;
        if ($messageStream !== null) {
            $partStream = $this->streamFactory->getLimitedPartStream($messageStream, $partBuilder);
            $contentStream = $this->streamFactory->getLimitedContentStream($messageStream, $partBuilder);
        }
        return new NonMimePart(
            $this->partStreamFilterManagerFactory->newInstance(),
            $this->streamFactory,
            $partStream,
            $contentStream
        );
    }
}
