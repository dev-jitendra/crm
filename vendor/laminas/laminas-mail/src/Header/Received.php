<?php

namespace Laminas\Mail\Header;

use Laminas\Mail\Headers;

use function implode;
use function strtolower;


class Received implements HeaderInterface, MultipleHeadersInterface
{
    
    protected $value;

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (strtolower($name) !== 'received') {
            throw new Exception\InvalidArgumentException('Invalid header line for Received string');
        }

        return new static($value);
    }

    
    public function __construct($value = '')
    {
        if (! HeaderValue::isValid($value)) {
            throw new Exception\InvalidArgumentException('Invalid Received value provided');
        }
        $this->value = $value;
    }

    
    public function getFieldName()
    {
        return 'Received';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->value;
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
        return 'Received: ' . $this->getFieldValue();
    }

    
    public function toStringMultipleHeaders(array $headers)
    {
        $strings = [$this->toString()];
        foreach ($headers as $header) {
            if (! $header instanceof self) {
                throw new Exception\RuntimeException(
                    'The Received multiple header implementation can only accept an array of Received headers'
                );
            }
            $strings[] = $header->toString();
        }
        return implode(Headers::EOL, $strings);
    }
}
