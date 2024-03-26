<?php

namespace ZBateson\MailMimeParser;

use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\Helper\MessageHelperService;
use ZBateson\MailMimeParser\Message\Part\MimePart;
use ZBateson\MailMimeParser\Message\Part\MessagePart;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;
use ZBateson\MailMimeParser\Message\Part\PartStreamFilterManager;
use ZBateson\MailMimeParser\Message\PartFilter;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Stream\StreamFactory;


class Message extends MimePart
{
    
    protected $messageHelperService;

    
    public function __construct(
        PartStreamFilterManager $partStreamFilterManager,
        StreamFactory $streamFactory,
        PartFilterFactory $partFilterFactory,
        PartBuilder $partBuilder,
        MessageHelperService $messageHelperService,
        StreamInterface $stream = null,
        StreamInterface $contentStream = null
    ) {
        parent::__construct(
            $partStreamFilterManager,
            $streamFactory,
            $partFilterFactory,
            $partBuilder,
            $stream,
            $contentStream
        );
        $this->messageHelperService = $messageHelperService;
    }

    
    public static function from($handleOrString)
    {
        $mmp = new MailMimeParser();
        return $mmp->parse($handleOrString);
    }

    
    public function getTextPart($index = 0)
    {
        return $this->getPart(
            $index,
            $this->partFilterFactory->newFilterFromInlineContentType('text/plain')
        );
    }

    
    public function getTextPartCount()
    {
        return $this->getPartCount(
            $this->partFilterFactory->newFilterFromInlineContentType('text/plain')
        );
    }

    
    public function getHtmlPart($index = 0)
    {
        return $this->getPart(
            $index,
            $this->partFilterFactory->newFilterFromInlineContentType('text/html')
        );
    }

    
    public function getHtmlPartCount()
    {
        return $this->getPartCount(
            $this->partFilterFactory->newFilterFromInlineContentType('text/html')
        );
    }

    
    public function getAttachmentPart($index)
    {
        $attachments = $this->getAllAttachmentParts();
        if (!isset($attachments[$index])) {
            return null;
        }
        return $attachments[$index];
    }

    
    public function getAllAttachmentParts()
    {
        $parts = $this->getAllParts(
            $this->partFilterFactory->newFilterFromArray([
                'multipart' => PartFilter::FILTER_EXCLUDE
            ])
        );
        return array_values(array_filter(
            $parts,
            function ($part) {
                return !(
                    $part->isTextPart()
                    && $part->getContentDisposition() === 'inline'
                );
            }
        ));
    }

    
    public function getAttachmentCount()
    {
        return count($this->getAllAttachmentParts());
    }

    
    public function getTextStream($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $textPart = $this->getTextPart($index);
        if ($textPart !== null) {
            return $textPart->getContentStream($charset);
        }
        return null;
    }

    
    public function getTextResourceHandle($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        trigger_error("getTextResourceHandle is deprecated since version 1.2.1", E_USER_DEPRECATED);
        $textPart = $this->getTextPart($index);
        if ($textPart !== null) {
            return $textPart->getContentResourceHandle($charset);
        }
        return null;
    }

    
    public function getTextContent($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $part = $this->getTextPart($index);
        if ($part !== null) {
            return $part->getContent($charset);
        }
        return null;
    }

    
    public function getHtmlStream($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $htmlPart = $this->getHtmlPart($index);
        if ($htmlPart !== null) {
            return $htmlPart->getContentStream($charset);
        }
        return null;
    }

    
    public function getHtmlResourceHandle($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        trigger_error("getHtmlResourceHandle is deprecated since version 1.2.1", E_USER_DEPRECATED);
        $htmlPart = $this->getHtmlPart($index);
        if ($htmlPart !== null) {
            return $htmlPart->getContentResourceHandle($charset);
        }
        return null;
    }

    
    public function getHtmlContent($index = 0, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $part = $this->getHtmlPart($index);
        if ($part !== null) {
            return $part->getContent($charset);
        }
        return null;
    }

    
    public function isMime()
    {
        $contentType = $this->getHeaderValue('Content-Type');
        $mimeVersion = $this->getHeaderValue('Mime-Version');
        return ($contentType !== null || $mimeVersion !== null);
    }

    
    public function setTextPart($resource, $charset = 'UTF-8')
    {
        $this->messageHelperService
            ->getMultipartHelper()
            ->setContentPartForMimeType(
                $this, 'text/plain', $resource, $charset
            );
    }

    
    public function setHtmlPart($resource, $charset = 'UTF-8')
    {
        $this->messageHelperService
            ->getMultipartHelper()
            ->setContentPartForMimeType(
                $this, 'text/html', $resource, $charset
            );
    }

    
    public function removeTextPart($index = 0)
    {
        return $this->messageHelperService
            ->getMultipartHelper()
            ->removePartByMimeType(
                $this, 'text/plain', $index
            );
    }

    
    public function removeAllTextParts($keepOtherPartsAsAttachments = true)
    {
        return $this->messageHelperService
            ->getMultipartHelper()
            ->removeAllContentPartsByMimeType(
                $this, 'text/plain', $keepOtherPartsAsAttachments
            );
    }

    
    public function removeHtmlPart($index = 0)
    {
        return $this->messageHelperService
            ->getMultipartHelper()
            ->removePartByMimeType(
                $this, 'text/html', $index
            );
    }

    
    public function removeAllHtmlParts($keepOtherPartsAsAttachments = true)
    {
        return $this->messageHelperService
            ->getMultipartHelper()
            ->removeAllContentPartsByMimeType(
                $this, 'text/html', $keepOtherPartsAsAttachments
            );
    }

    
    public function addAttachmentPart($resource, $mimeType, $filename = null, $disposition = 'attachment', $encoding = 'base64')
    {
        $this->messageHelperService
            ->getMultipartHelper()
            ->createAndAddPartForAttachment($this, $resource, $mimeType, $disposition, $filename, $encoding);
    }

    
    public function addAttachmentPartFromFile($filePath, $mimeType, $filename = null, $disposition = 'attachment', $encoding = 'base64')
    {
        $handle = Psr7\stream_for(fopen($filePath, 'r'));
        if ($filename === null) {
            $filename = basename($filePath);
        }
        $this->addAttachmentPart($handle, $mimeType, $filename, $disposition, $encoding);
    }

    
    public function removeAttachmentPart($index)
    {
        $part = $this->getAttachmentPart($index);
        $this->removePart($part);
    }

    
    public function getSignedMessageStream()
    {
        return $this
            ->messageHelperService
            ->getPrivacyHelper()
            ->getSignedMessageStream($this);
    }

    
    public function getSignedMessageAsString()
    {
        return $this
            ->messageHelperService
            ->getPrivacyHelper()
            ->getSignedMessageAsString($this);
    }

    
    public function getSignaturePart()
    {
        return $this
            ->messageHelperService
            ->getPrivacyHelper()
            ->getSignaturePart($this);
    }

    
    public function setAsMultipartSigned($micalg, $protocol)
    {
        $this
            ->messageHelperService
            ->getPrivacyHelper()
            ->setMessageAsMultipartSigned($this, $micalg, $protocol);
    }

    
    public function setSignature($body)
    {
        $this->messageHelperService->getPrivacyHelper()
            ->setSignature($this, $body);
    }
}
