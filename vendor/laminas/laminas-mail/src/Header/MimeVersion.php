<?php

namespace Laminas\Mail\Header;

use function in_array;
use function preg_match;
use function strtolower;

class MimeVersion implements HeaderInterface
{
    
    protected $version = '1.0';

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (! in_array(strtolower($name), ['mimeversion', 'mime_version', 'mime-version'])) {
            throw new Exception\InvalidArgumentException('Invalid header line for MIME-Version string');
        }

        
        $header = new static();
        if (preg_match('/^(?P<version>\d+\.\d+)$/', $value, $matches)) {
            $header->setVersion($matches['version']);
        }

        return $header;
    }

    
    public function getFieldName()
    {
        return 'MIME-Version';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->version;
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
        return 'MIME-Version: ' . $this->getFieldValue();
    }

    
    public function setVersion($version)
    {
        if (! preg_match('/^[1-9]\d*\.\d+$/', $version)) {
            throw new Exception\InvalidArgumentException('Invalid MIME-Version value detected');
        }
        $this->version = $version;
        return $this;
    }

    
    public function getVersion()
    {
        return $this->version;
    }
}
