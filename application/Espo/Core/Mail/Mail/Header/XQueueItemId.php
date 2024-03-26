<?php


namespace Espo\Core\Mail\Mail\Header;

use Laminas\Mail\Header;

class XQueueItemId implements Header\HeaderInterface
{
    private string $fieldName = 'X-Queue-Item-Id';

    private ?string $id = null;

    
    public static function fromString($headerLine)
    {
        list($name, $value) = Header\GenericHeader::splitHeaderLine($headerLine);

        $valueDecoded = Header\HeaderWrap::mimeDecodeValue($value);

        if (strtolower($name) !== 'x-queue-item-id') {
            throw new Header\Exception\InvalidArgumentException('Invalid header line for x-queue-item-id string');
        }

        $header = new self();

        $header->setId($valueDecoded);

        return $header;
    }

    
    public function getFieldName()
    {
        return $this->fieldName;
    }

    
    public function setFieldName($value)
    {
    }

    
    public function setEncoding($encoding)
    {
        return $this;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    
    public function getEncoding()
    {
        return 'ASCII';
    }

    
    public function toString()
    {
        return $this->fieldName . ': ' . $this->getFieldValue();
    }

    
    public function getFieldValue($format = Header\HeaderInterface::FORMAT_RAW)
    {
        return $this->id;
    }
}
