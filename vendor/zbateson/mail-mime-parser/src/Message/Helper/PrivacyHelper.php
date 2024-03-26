<?php

namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\Message;
use ZBateson\MailMimeParser\Message\Part\Factory\MimePartFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\UUEncodedPartFactory;
use ZBateson\MailMimeParser\Message\Part\ParentPart;
use ZBateson\MailMimeParser\Message\PartFilter;


class PrivacyHelper extends AbstractHelper
{
    
    private $genericHelper;

    
    private $multipartHelper;

    
    public function __construct(
        MimePartFactory $mimePartFactory,
        UUEncodedPartFactory $uuEncodedPartFactory,
        PartBuilderFactory $partBuilderFactory,
        GenericHelper $genericHelper,
        MultipartHelper $multipartHelper
    ) {
        parent::__construct($mimePartFactory, $uuEncodedPartFactory, $partBuilderFactory);
        $this->genericHelper = $genericHelper;
        $this->multipartHelper = $multipartHelper;
    }

    
    public function setMessageAsMultipartSigned(Message $message, $micalg, $protocol)
    {
        if (strcasecmp($message->getContentType(), 'multipart/signed') !== 0) {
            $this->multipartHelper->enforceMime($message);
            $messagePart = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory)->createMessagePart();
            $this->genericHelper->movePartContentAndChildren($message, $messagePart);
            $message->addChild($messagePart);
            $boundary = $this->multipartHelper->getUniqueBoundary('multipart/signed');
            $message->setRawHeader(
                'Content-Type',
                "multipart/signed;\r\n\tboundary=\"$boundary\";\r\n\tmicalg=\"$micalg\"; protocol=\"$protocol\""
            );
        }
        $this->overwrite8bitContentEncoding($message);
        $this->ensureHtmlPartFirstForSignedMessage($message);
        $this->setSignature($message, 'Empty');
    }

    
    public function setSignature(Message $message, $body)
    {
        $signedPart = $message->getSignaturePart();
        if ($signedPart === null) {
            $signedPart = $this->partBuilderFactory->newPartBuilder($this->mimePartFactory)->createMessagePart();
            $message->addChild($signedPart);
        }
        $signedPart->setRawHeader(
            'Content-Type',
            $message->getHeaderParameter('Content-Type', 'protocol')
        );
        $signedPart->setContent($body);
    }

    
    public function overwrite8bitContentEncoding(Message $message)
    {
        $parts = $message->getAllParts(new PartFilter([
            'headers' => [ PartFilter::FILTER_INCLUDE => [
                'Content-Transfer-Encoding' => '8bit'
            ] ]
        ]));
        foreach ($parts as $part) {
            $contentType = strtolower($part->getContentType());
            if ($contentType === 'text/plain' || $contentType === 'text/html') {
                $part->setRawHeader('Content-Transfer-Encoding', 'quoted-printable');
            } else {
                $part->setRawHeader('Content-Transfer-Encoding', 'base64');
            }
        }
    }

    
    public function ensureHtmlPartFirstForSignedMessage(Message $message)
    {
        $alt = $message->getPartByMimeType('multipart/alternative');
        if ($alt !== null && $alt instanceof ParentPart) {
            $cont = $this->multipartHelper->getContentPartContainerFromAlternative('text/html', $alt);
            $children = $alt->getChildParts();
            $pos = array_search($cont, $children, true);
            if ($pos !== false && $pos !== 0) {
                $alt->removePart($children[0]);
                $alt->addChild($children[0]);
            }
        }
    }

    
    public function getSignedMessageStream(Message $message)
    {
        $child = $message->getChild(0);
        if ($child !== null) {
            return $child->getStream();
        }
        return null;
    }

    
    public function getSignedMessageAsString(Message $message)
    {
        $stream = $this->getSignedMessageStream($message);
        if ($stream !== null) {
            return preg_replace(
                '/\r\n|\r|\n/',
                "\r\n",
                $stream->getContents()
            );
        }
        return null;
    }

    
    public function getSignaturePart(Message $message)
    {
        if (strcasecmp($message->getContentType(), 'multipart/signed') === 0) {
            return $message->getChild(1);
        } else {
            return null;
        }
    }
}
