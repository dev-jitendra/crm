<?php

namespace ZBateson\MailMimeParser\Message;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;
use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartFactoryService;
use GuzzleHttp\Psr7\StreamWrapper;


class MessageParser
{
    
    protected $partFactoryService;
    
    
    protected $partBuilderFactory;
    
    
    private $lastLineSeparatorLength = 0;
    
    
    public function __construct(
        PartFactoryService $pfs,
        PartBuilderFactory $pbf
    ) {
        $this->partFactoryService = $pfs;
        $this->partBuilderFactory = $pbf;
    }
    
    
    public function parse(StreamInterface $stream)
    {
        $partBuilder = $this->read($stream);
        return $partBuilder->createMessagePart($stream);
    }
    
    
    private function addRawHeaderToPart($header, PartBuilder $partBuilder)
    {
        if ($header !== '' && strpos($header, ':') !== false) {
            $a = explode(':', $header, 2);
            $partBuilder->addHeader($a[0], trim($a[1]));
        }
    }

    
    private function readLine($handle)
    {
        $size = 4096;
        $ret = $line = fgets($handle, $size);
        while (strlen($line) === $size - 1 && substr($line, -1) !== "\n") {
            $line = fgets($handle, $size);
        }
        return $ret;
    }

    
    private function readBoundaryLine($handle, &$lineSeparatorLength = 0)
    {
        $size = 2048;
        $isCut = false;
        $line = fgets($handle, $size);
        while (strlen($line) === $size - 1 && substr($line, -1) !== "\n") {
            $line = fgets($handle, $size);
            $isCut = true;
        }
        $ret = rtrim($line, "\r\n");
        $lineSeparatorLength = strlen($line) - strlen($ret);
        return ($isCut) ? '' : $ret;
    }

    
    protected function readHeaders($handle, PartBuilder $partBuilder)
    {
        $header = '';
        do {
            $line = $this->readLine($handle);
            if (empty($line) || $line[0] !== "\t" && $line[0] !== ' ') {
                $this->addRawHeaderToPart($header, $partBuilder);
                $header = '';
            } else {
                $line = "\r\n" . $line;
            }
            $header .= rtrim($line, "\r\n");
        } while ($header !== '');
    }

    
    private function findContentBoundary($handle, PartBuilder $partBuilder)
    {
        
        
        while (!feof($handle)) {
            $endPos = ftell($handle) - $this->lastLineSeparatorLength;
            $line = $this->readBoundaryLine($handle, $this->lastLineSeparatorLength);
            if ($line !== '' && $partBuilder->setEndBoundaryFound($line)) {
                $partBuilder->setStreamPartAndContentEndPos($endPos);
                return;
            }
        }
        $partBuilder->setStreamPartAndContentEndPos(ftell($handle));
        $partBuilder->setEof();
    }
    
    
    protected function readUUEncodedOrPlainTextMessage($handle, PartBuilder $partBuilder)
    {
        $partBuilder->setStreamContentStartPos(ftell($handle));
        $part = $partBuilder;
        while (!feof($handle)) {
            $start = ftell($handle);
            $line = trim($this->readLine($handle));
            if (preg_match('/^begin ([0-7]{3}) (.*)$/', $line, $matches)) {
                $part = $this->partBuilderFactory->newPartBuilder(
                    $this->partFactoryService->getUUEncodedPartFactory()
                );
                $part->setStreamPartStartPos($start);
                
                $part->setStreamContentStartPos($start);
                $part->setProperty('mode', $matches[1]);
                $part->setProperty('filename', $matches[2]);
                $partBuilder->addChild($part);
            }
            $part->setStreamPartAndContentEndPos(ftell($handle));
        }
        $partBuilder->setStreamPartEndPos(ftell($handle));
    }
    
    
    private function readPartContent($handle, PartBuilder $partBuilder)
    {
        $partBuilder->setStreamContentStartPos(ftell($handle));
        $this->findContentBoundary($handle, $partBuilder);
        if ($partBuilder->isMultiPart()) {
            while (!$partBuilder->isParentBoundaryFound()) {
                $child = $this->partBuilderFactory->newPartBuilder(
                    $this->partFactoryService->getMimePartFactory()
                );
                $partBuilder->addChild($child);
                $this->readPart($handle, $child);
            }
        }
    }
    
    
    protected function readPart($handle, PartBuilder $partBuilder)
    {
        $partBuilder->setStreamPartStartPos(ftell($handle));
        
        if ($partBuilder->canHaveHeaders()) {
            $this->readHeaders($handle, $partBuilder);
            $this->lastLineSeparatorLength = 0;
        }
        if ($partBuilder->getParent() === null && !$partBuilder->isMime()) {
            $this->readUUEncodedOrPlainTextMessage($handle, $partBuilder);
        } else {
            $this->readPartContent($handle, $partBuilder);
        }
    }
    
    
    protected function read(StreamInterface $stream)
    {
        $partBuilder = $this->partBuilderFactory->newPartBuilder(
            $this->partFactoryService->getMessageFactory()
        );
        
        
        
        $this->readPart(StreamWrapper::getResource($stream), $partBuilder);
        return $partBuilder;
    }
}
