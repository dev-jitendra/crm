<?php

namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Message;
use ZBateson\MailMimeParser\Message\Part\MimePart;
use ZBateson\MailMimeParser\Message\Part\ParentHeaderPart;


class GenericHelper extends AbstractHelper
{
    
    private static $contentHeaders = [
        'Content-Type',
        'Content-Transfer-Encoding',
        'Content-Disposition',
        'Content-ID',
        'Content-Description',
        'Content-Language',
        'Content-Base',
        'Content-Location',
        'Content-features',
        'Content-Alternative',
        'Content-MD5',
        'Content-Duration'
    ];
    
    
    public function copyHeader(ParentHeaderPart $from, ParentHeaderPart $to, $header, $default = null)
    {
        $fromHeader = $from->getHeader($header);
        $set = ($fromHeader !== null) ? $fromHeader->getRawValue() : $default;
        if ($set !== null) {
            $to->setRawHeader($header, $set);
        }
    }

    
    public function removeContentHeadersAndContent(ParentHeaderPart $part)
    {
        foreach (self::$contentHeaders as $header) {
            $part->removeHeader($header);
        }
        $part->detachContentStream();
    }

    
    public function copyContentHeadersAndContent(ParentHeaderPart $from, ParentHeaderPart $to, $move = false)
    {
        $this->copyHeader($from, $to, 'Content-Type', 'text/plain; charset=utf-8');
        if ($from->getHeader('Content-Type') === null) {
            $this->copyHeader($from, $to, 'Content-Transfer-Encoding', 'quoted-printable');
        } else {
            $this->copyHeader($from, $to, 'Content-Transfer-Encoding');
        }
        $rem = array_diff(self::$contentHeaders, [ 'Content-Type', 'Content-Transfer-Encoding']);
        foreach ($rem as $header) {
            $this->copyHeader($from, $to, $header);
        }
        if ($from->hasContent()) {
            $to->attachContentStream($from->getContentStream(), MailMimeParser::DEFAULT_CHARSET);
        }
        if ($move) {
            $this->removeContentHeadersAndContent($from);
        }
    }

    
    public function createNewContentPartFrom(ParentHeaderPart $part)
    {
        $mime = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory)->createMessagePart();
        $this->copyContentHeadersAndContent($part, $mime, true);
        return $mime;
    }

    
    public function movePartContentAndChildren(ParentHeaderPart $from, ParentHeaderPart $to)
    {
        $this->copyContentHeadersAndContent($from, $to, true);
        foreach ($from->getChildParts() as $child) {
            $from->removePart($child);
            $to->addChild($child);
        }
    }

    
    public function replacePart(Message $message, ParentHeaderPart $part, ParentHeaderPart $replacement)
    {
        $position = $message->removePart($replacement);
        if ($part === $message) {
            $this->movePartContentAndChildren($replacement, $part);
            return;
        }
        $parent = $part->getParent();
        $parent->addChild($replacement, $position);
    }
}
