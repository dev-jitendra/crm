<?php

namespace Laminas\Mail\Header;

use Laminas\Mail\Headers;

use function array_map;
use function explode;
use function implode;
use function preg_match;
use function sprintf;
use function strtolower;
use function trim;



abstract class IdentificationField implements HeaderInterface
{
    
    protected static $type;

    
    protected $messageIds;

    
    protected $fieldName;

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        if (strtolower($name) !== static::$type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                self::class
            ));
        }

        $value = HeaderWrap::mimeDecodeValue($value);

        $messageIds = array_map(
            [self::class, "trimMessageId"],
            explode(" ", $value)
        );

        $header = new static();
        $header->setIds($messageIds);

        return $header;
    }

    
    private static function trimMessageId($id)
    {
        return trim($id, "\t\n\r\0\x0B<>");
    }

    
    public function getFieldName()
    {
        return $this->fieldName;
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return implode(Headers::FOLDING, array_map(static fn($id) => sprintf('<%s>', $id), $this->messageIds));
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
        return sprintf('%s: %s', $this->getFieldName(), $this->getFieldValue());
    }

    
    public function setIds($ids)
    {
        foreach ($ids as $id) {
            if (
                ! HeaderValue::isValid($id)
                || preg_match("/[\r\n]/", $id)
            ) {
                throw new Exception\InvalidArgumentException('Invalid ID detected');
            }
        }

        $this->messageIds = array_map([self::class, "trimMessageId"], $ids);
        return $this;
    }

    
    public function getIds()
    {
        return $this->messageIds;
    }
}
