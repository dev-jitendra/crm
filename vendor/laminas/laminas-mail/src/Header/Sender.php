<?php

namespace Laminas\Mail\Header;

use Laminas\Mail;
use Laminas\Mail\Address\AddressInterface;
use Laminas\Mime\Mime;

use function gettype;
use function is_object;
use function is_string;
use function preg_match;
use function sprintf;
use function strtolower;
use function trim;


class Sender implements HeaderInterface
{
    
    protected $address;

    
    protected $encoding;

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (strtolower($name) !== 'sender') {
            throw new Exception\InvalidArgumentException('Invalid header name for Sender string');
        }

        $header = new static();

        
        $hasMatches = preg_match(
            '/^(?:(?P<name>.+)\s)?(?(name)<|<?)(?P<email>[^\s]+?)(?(name)>|>?)$/',
            $value,
            $matches
        );

        if ($hasMatches !== 1) {
            throw new Exception\InvalidArgumentException('Invalid header value for Sender string');
        }

        $senderName = trim($matches['name']);

        if (empty($senderName)) {
            $senderName = null;
        }

        $header->setAddress($matches['email'], $senderName);

        return $header;
    }

    
    public function getFieldName()
    {
        return 'Sender';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        if (! $this->address instanceof Mail\Address\AddressInterface) {
            return '';
        }

        $email = sprintf('<%s>', $this->address->getEmail());
        $name  = $this->address->getName();

        if (! empty($name)) {
            if ($format == HeaderInterface::FORMAT_ENCODED) {
                $encoding = $this->getEncoding();
                if ('ASCII' !== $encoding) {
                    $name = HeaderWrap::mimeEncodeValue($name, $encoding);
                }
            }
            $email = sprintf('%s %s', $name, $email);
        }

        return $email;
    }

    
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    
    public function getEncoding()
    {
        if (! $this->encoding) {
            $this->encoding = Mime::isPrintable($this->getFieldValue(HeaderInterface::FORMAT_RAW))
                ? 'ASCII'
                : 'UTF-8';
        }

        return $this->encoding;
    }

    
    public function toString()
    {
        return 'Sender: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }

    
    public function setAddress($emailOrAddress, $name = null)
    {
        if (is_string($emailOrAddress)) {
            $emailOrAddress = new Mail\Address($emailOrAddress, $name);
        } elseif (! $emailOrAddress instanceof Mail\Address\AddressInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string or AddressInterface object; received "%s"',
                __METHOD__,
                is_object($emailOrAddress) ? $emailOrAddress::class : gettype($emailOrAddress)
            ));
        }
        $this->address = $emailOrAddress;
        return $this;
    }

    
    public function getAddress()
    {
        return $this->address;
    }
}
