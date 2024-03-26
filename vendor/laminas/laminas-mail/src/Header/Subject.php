<?php

namespace Laminas\Mail\Header;

use Laminas\Mime\Mime;

use function strtolower;
use function strtoupper;


class Subject implements UnstructuredInterface
{
    
    protected $subject = '';

    
    protected $encoding;

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (strtolower($name) !== 'subject') {
            throw new Exception\InvalidArgumentException('Invalid header line for Subject string');
        }

        $header = new static();
        $header->setSubject($value);

        return $header;
    }

    
    public function getFieldName()
    {
        return 'Subject';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        if (HeaderInterface::FORMAT_ENCODED === $format) {
            return HeaderWrap::wrap($this->subject, $this);
        }

        return $this->subject;
    }

    
    public function setEncoding($encoding)
    {
        if ($encoding === $this->encoding) {
            return $this;
        }

        if ($encoding === null) {
            $this->encoding = null;
            return $this;
        }

        $encoding = strtoupper($encoding);
        if ($encoding === 'UTF-8') {
            $this->encoding = $encoding;
            return $this;
        }

        if ($encoding === 'ASCII' && Mime::isPrintable($this->subject)) {
            $this->encoding = $encoding;
            return $this;
        }

        $this->encoding = null;

        return $this;
    }

    
    public function getEncoding()
    {
        if (! $this->encoding) {
            $this->encoding = Mime::isPrintable($this->subject) ? 'ASCII' : 'UTF-8';
        }

        return $this->encoding;
    }

    
    public function setSubject($subject)
    {
        $subject = (string) $subject;

        if (! HeaderWrap::canBeEncoded($subject)) {
            throw new Exception\InvalidArgumentException(
                'Subject value must be composed of printable US-ASCII or UTF-8 characters.'
            );
        }

        $this->subject  = $subject;
        $this->encoding = null;

        return $this;
    }

    
    public function toString()
    {
        return 'Subject: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }
}
