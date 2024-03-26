<?php

namespace Laminas\Mail\Header;

use function strtolower;


class Date implements HeaderInterface
{
    
    protected $value;

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (strtolower($name) !== 'date') {
            throw new Exception\InvalidArgumentException('Invalid header line for Date string');
        }

        return new static($value);
    }

    
    public function __construct($value)
    {
        if (! HeaderValue::isValid($value)) {
            throw new Exception\InvalidArgumentException('Invalid Date header value detected');
        }
        $this->value = $value;
    }

    
    public function getFieldName()
    {
        return 'Date';
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
        return 'Date: ' . $this->getFieldValue();
    }
}
