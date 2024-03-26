<?php

namespace Laminas\Mail\Header;

use function implode;
use function in_array;
use function sprintf;
use function strtolower;

class ContentTransferEncoding implements HeaderInterface
{
    
    protected static $allowedTransferEncodings = [
        '7bit',
        '8bit',
        'quoted-printable',
        'base64',
        'binary',
        
    ];

    
    protected $transferEncoding;

    
    protected $parameters = [];

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (
            ! in_array(
                strtolower($name),
                ['contenttransferencoding', 'content_transfer_encoding', 'content-transfer-encoding']
            )
        ) {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Transfer-Encoding string');
        }

        $header = new static();
        $header->setTransferEncoding($value);

        return $header;
    }

    
    public function getFieldName()
    {
        return 'Content-Transfer-Encoding';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->transferEncoding;
    }

    
    public function setEncoding($encoding)
    {
        
        return $this;
    }

    
    public function getEncoding()
    {
        return 'ASCII';
    }

    
    public function toString()
    {
        return 'Content-Transfer-Encoding: ' . $this->getFieldValue();
    }

    
    public function setTransferEncoding($transferEncoding)
    {
        
        $transferEncoding = strtolower($transferEncoding);

        if (! in_array($transferEncoding, static::$allowedTransferEncodings)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects one of "' . implode(', ', static::$allowedTransferEncodings) . '"; received "%s"',
                __METHOD__,
                (string) $transferEncoding
            ));
        }
        $this->transferEncoding = $transferEncoding;
        return $this;
    }

    
    public function getTransferEncoding()
    {
        return $this->transferEncoding;
    }
}
