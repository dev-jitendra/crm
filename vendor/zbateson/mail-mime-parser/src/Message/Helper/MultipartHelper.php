<?php

namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\Message;
use ZBateson\MailMimeParser\Message\Part\Factory\MimePartFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\UUEncodedPartFactory;
use ZBateson\MailMimeParser\Message\Part\MessagePart;
use ZBateson\MailMimeParser\Message\Part\MimePart;
use ZBateson\MailMimeParser\Message\Part\ParentHeaderPart;
use ZBateson\MailMimeParser\Message\PartFilter;


class MultipartHelper extends AbstractHelper
{
    
    private $genericHelper;

    
    public function __construct(
        MimePartFactory $mimePartFactory,
        UUEncodedPartFactory $uuEncodedPartFactory,
        PartBuilderFactory $partBuilderFactory,
        GenericHelper $genericHelper
    ) {
        parent::__construct($mimePartFactory, $uuEncodedPartFactory, $partBuilderFactory);
        $this->genericHelper = $genericHelper;
    }

    
    public function getUniqueBoundary($mimeType)
    {
        $type = ltrim(strtoupper(preg_replace('/^(multipart\/(.{3}).*|.*)$/i', '$2-', $mimeType)), '-');
        return uniqid('----=MMP-' . $type . '.', true);
    }

    
    public function setMimeHeaderBoundaryOnPart(ParentHeaderPart $part, $mimeType)
    {
        $part->setRawHeader(
            'Content-Type',
            "$mimeType;\r\n\tboundary=\""
                . $this->getUniqueBoundary($mimeType) . '"'
        );
    }

    
    public function setMessageAsMixed(Message $message)
    {
        if ($message->hasContent()) {
            $part = $this->genericHelper->createNewContentPartFrom($message);
            $message->addChild($part, 0);
        }
        $this->setMimeHeaderBoundaryOnPart($message, 'multipart/mixed');
        $atts = $message->getAllAttachmentParts();
        if (!empty($atts)) {
            foreach ($atts as $att) {
                $att->markAsChanged();
            }
        }
    }

    
    public function setMessageAsAlternative(Message $message)
    {
        if ($message->hasContent()) {
            $part = $this->genericHelper->createNewContentPartFrom($message);
            $message->addChild($part, 0);
        }
        $this->setMimeHeaderBoundaryOnPart($message, 'multipart/alternative');
    }

    
    public function getContentPartContainerFromAlternative($mimeType, ParentHeaderPart $alternativePart)
    {
        $part = $alternativePart->getPart(0, PartFilter::fromInlineContentType($mimeType));
        $contPart = null;
        do {
            if ($part === null) {
                return false;
            }
            $contPart = $part;
            $part = $part->getParent();
        } while ($part !== $alternativePart);
        return $contPart;
    }

    
    public function removeAllContentPartsFromAlternative(Message $message, $mimeType, ParentHeaderPart $alternativePart, $keepOtherContent)
    {
        $rmPart = $this->getContentPartContainerFromAlternative($mimeType, $alternativePart);
        if ($rmPart === false) {
            return false;
        }
        if ($keepOtherContent) {
            $this->moveAllPartsAsAttachmentsExcept($message, $rmPart, $mimeType);
            $alternativePart = $message->getPart(0, PartFilter::fromInlineContentType('multipart/alternative'));
        } else {
            $rmPart->removeAllParts();
        }
        $message->removePart($rmPart);
        if ($alternativePart !== null) {
            if ($alternativePart->getChildCount() === 1) {
                $this->genericHelper->replacePart($message, $alternativePart, $alternativePart->getChild(0));
            } elseif ($alternativePart->getChildCount() === 0) {
                $message->removePart($alternativePart);
            }
        }
        while ($message->getChildCount() === 1) {
            $this->genericHelper->replacePart($message, $message, $message->getChild(0));
        }
        return true;
    }

    
    public function createAlternativeContentPart(Message $message, MessagePart $contentPart)
    {
        $altPart = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory)->createMessagePart();
        $this->setMimeHeaderBoundaryOnPart($altPart, 'multipart/alternative');
        $message->removePart($contentPart);
        $message->addChild($altPart, 0);
        $altPart->addChild($contentPart, 0);
        return $altPart;
    }

    
    public function moveAllPartsAsAttachmentsExcept(Message $message, ParentHeaderPart $from, $exceptMimeType)
    {
        $parts = $from->getAllParts(new PartFilter([
            'multipart' => PartFilter::FILTER_EXCLUDE,
            'headers' => [
                PartFilter::FILTER_EXCLUDE => [
                    'Content-Type' => $exceptMimeType
                ]
            ]
        ]));
        if (strcasecmp($message->getContentType(), 'multipart/mixed') !== 0) {
            $this->setMessageAsMixed($message);
        }
        foreach ($parts as $part) {
            $from->removePart($part);
            $message->addChild($part);
        }
    }

    
    public function enforceMime(Message $message)
    {
        if (!$message->isMime()) {
            if ($message->getAttachmentCount()) {
                $this->setMessageAsMixed($message);
            } else {
                $message->setRawHeader('Content-Type', "text/plain;\r\n\tcharset=\"iso-8859-1\"");
            }
            $message->setRawHeader('Mime-Version', '1.0');
        }
    }

    
    public function createMultipartRelatedPartForInlineChildrenOf(ParentHeaderPart $parent)
    {
        $relatedPart = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory)->createMessagePart();
        $this->setMimeHeaderBoundaryOnPart($relatedPart, 'multipart/related');
        foreach ($parent->getChildParts(PartFilter::fromDisposition('inline', PartFilter::FILTER_EXCLUDE)) as $part) {
            $parent->removePart($part);
            $relatedPart->addChild($part);
        }
        $parent->addChild($relatedPart, 0);
        return $relatedPart;
    }

    
    public function findOtherContentPartFor(Message $message, $mimeType)
    {
        $altPart = $message->getPart(
            0,
            PartFilter::fromInlineContentType(($mimeType === 'text/plain') ? 'text/html' : 'text/plain')
        );
        if ($altPart !== null && $altPart->getParent() !== null && $altPart->getParent()->isMultiPart()) {
            $altPartParent = $altPart->getParent();
            if ($altPartParent->getChildCount(PartFilter::fromDisposition('inline', PartFilter::FILTER_EXCLUDE)) !== 1) {
                $altPart = $this->createMultipartRelatedPartForInlineChildrenOf($altPartParent);
            }
        }
        return $altPart;
    }

    
    public function createContentPartForMimeType(Message $message, $mimeType, $charset)
    {
        $builder = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory);
        $builder->addHeader('Content-Type', "$mimeType;\r\n\tcharset=\"$charset\"");
        $builder->addHeader('Content-Transfer-Encoding', 'quoted-printable');
        $this->enforceMime($message);
        $mimePart = $builder->createMessagePart();

        $altPart = $this->findOtherContentPartFor($message, $mimeType);

        if ($altPart === $message) {
            $this->setMessageAsAlternative($message);
            $message->addChild($mimePart);
        } elseif ($altPart !== null) {
            $mimeAltPart = $this->createAlternativeContentPart($message, $altPart);
            $mimeAltPart->addChild($mimePart, 1);
        } else {
            $message->addChild($mimePart, 0);
        }

        return $mimePart;
    }

    
    public function createAndAddPartForAttachment(Message $message, $resource, $mimeType, $disposition, $filename = null, $encoding = 'base64')
    {
        if ($filename === null) {
            $filename = 'file' . uniqid();
        }

        $safe = iconv('UTF-8', 'US-ASCII
        if ($message->isMime()) {
            $builder = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory);
            $builder->addHeader('Content-Transfer-Encoding', $encoding);
            if (strcasecmp($message->getContentType(), 'multipart/mixed') !== 0) {
                $this->setMessageAsMixed($message);
            }
            $builder->addHeader('Content-Type', "$mimeType;\r\n\tname=\"$safe\"");
            $builder->addHeader('Content-Disposition', "$disposition;\r\n\tfilename=\"$safe\"");
        } else {
            $builder = $this->partBuilderFactory->newPartBuilder(
                $this->uuEncodedPartFactory
            );
            $builder->setProperty('filename', $safe);
        }
        $part = $builder->createMessagePart();
        $part->setContent($resource);
        $message->addChild($part);
    }

    
    public function removeAllContentPartsByMimeType(Message $message, $mimeType, $keepOtherContent = false)
    {
        $alt = $message->getPart(0, PartFilter::fromInlineContentType('multipart/alternative'));
        if ($alt !== null) {
            return $this->removeAllContentPartsFromAlternative($message, $mimeType, $alt, $keepOtherContent);
        }
        $message->removeAllParts(PartFilter::fromInlineContentType($mimeType));
        return true;
    }

    
    public function removePartByMimeType(Message $message, $mimeType, $index = 0)
    {
        $parts = $message->getAllParts(PartFilter::fromInlineContentType($mimeType));
        $alt = $message->getPart(0, PartFilter::fromInlineContentType('multipart/alternative'));
        if ($parts === null || !isset($parts[$index])) {
            return false;
        } elseif (count($parts) === 1) {
            return $this->removeAllContentPartsByMimeType($message, $mimeType, true);
        }
        $part = $parts[$index];
        $message->removePart($part);
        if ($alt !== null && $alt->getChildCount() === 1) {
            $this->genericHelper->replacePart($message, $alt, $alt->getChild(0));
        }
        return true;
    }

    
    public function setContentPartForMimeType(Message $message, $mimeType, $stringOrHandle, $charset)
    {
        $part = ($mimeType === 'text/html') ? $message->getHtmlPart() : $message->getTextPart();
        if ($part === null) {
            $part = $this->createContentPartForMimeType($message, $mimeType, $charset);
        } else {
            $contentType = $part->getContentType();
            $part->setRawHeader('Content-Type', "$contentType;\r\n\tcharset=\"$charset\"");
        }
        $part->setContent($stringOrHandle);
    }
}
