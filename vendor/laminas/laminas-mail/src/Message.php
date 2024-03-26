<?php

namespace Laminas\Mail;

use ArrayIterator;
use Laminas\Mail\Header\Bcc;
use Laminas\Mail\Header\Cc;
use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Header\From;
use Laminas\Mail\Header\MimeVersion;
use Laminas\Mail\Header\ReplyTo;
use Laminas\Mail\Header\Sender;
use Laminas\Mail\Header\To;
use Laminas\Mime;
use Traversable;

use function array_shift;
use function count;
use function date;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;

class Message
{
    
    protected $body;

    
    protected $headers;

    
    protected $encoding = 'ASCII';

    
    public function isValid()
    {
        $from = $this->getFrom();
        if (! $from instanceof AddressList) {
            return false;
        }
        return (bool) count($from);
    }

    
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        $this->getHeaders()->setEncoding($encoding);
        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
        $headers->setEncoding($this->getEncoding());
        return $this;
    }

    
    public function getHeaders()
    {
        if (null === $this->headers) {
            $this->setHeaders(new Headers());
            $date = Header\Date::fromString('Date: ' . date('r'));
            $this->headers->addHeader($date);
        }
        return $this->headers;
    }

    
    public function setFrom($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('from');
        return $this->addFrom($emailOrAddressList, $name);
    }

    
    public function addFrom($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->getFrom();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    
    public function getFrom()
    {
        return $this->getAddressListFromHeader('from', From::class);
    }

    
    public function setTo($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('to');
        return $this->addTo($emailOrAddressList, $name);
    }

    
    public function addTo($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->getTo();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    
    public function getTo()
    {
        return $this->getAddressListFromHeader('to', To::class);
    }

    
    public function setCc($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('cc');
        return $this->addCc($emailOrAddressList, $name);
    }

    
    public function addCc($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->getCc();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    
    public function getCc()
    {
        return $this->getAddressListFromHeader('cc', Cc::class);
    }

    
    public function setBcc($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('bcc');
        return $this->addBcc($emailOrAddressList, $name);
    }

    
    public function addBcc($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->getBcc();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    
    public function getBcc()
    {
        return $this->getAddressListFromHeader('bcc', Bcc::class);
    }

    
    public function setReplyTo($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('reply-to');
        return $this->addReplyTo($emailOrAddressList, $name);
    }

    
    public function addReplyTo($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->getReplyTo();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    
    public function getReplyTo()
    {
        return $this->getAddressListFromHeader('reply-to', ReplyTo::class);
    }

    
    public function setSender(mixed $emailOrAddress, mixed $name = null)
    {
        
        $header = $this->getHeaderByName('sender', Sender::class);
        $header->setAddress($emailOrAddress, $name);
        return $this;
    }

    
    public function getSender()
    {
        $headers = $this->getHeaders();
        if (! $headers->has('sender')) {
            return null;
        }

        
        $header = $this->getHeaderByName('sender', Sender::class);
        return $header->getAddress();
    }

    
    public function setSubject($subject)
    {
        $headers = $this->getHeaders();
        if (! $headers->has('subject')) {
            $header = new Header\Subject();
            $headers->addHeader($header);
        } else {
            $header = $headers->get('subject');
        }
        $header->setSubject($subject);
        $header->setEncoding($this->getEncoding());
        return $this;
    }

    
    public function getSubject()
    {
        $headers = $this->getHeaders();
        if (! $headers->has('subject')) {
            return;
        }
        $header = $headers->get('subject');
        return $header->getFieldValue();
    }

    
    public function setBody($body)
    {
        if (! is_string($body) && $body !== null) {
            if (! is_object($body)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s expects a string or object argument; received "%s"',
                    __METHOD__,
                    gettype($body)
                ));
            }
            if (! $body instanceof Mime\Message) {
                if (! method_exists($body, '__toString')) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        '%s expects object arguments of type %s or implementing __toString();'
                        . ' object of type "%s" received',
                        __METHOD__,
                        Mime\Message::class,
                        $body::class
                    ));
                }
            }
        }
        $this->body = $body;

        if (! $this->body instanceof Mime\Message) {
            return $this;
        }

        
        $headers = $this->getHeaders();
        $this->getHeaderByName('mime-version', MimeVersion::class);

        
        if ($this->body->isMultiPart()) {
            $mime = $this->body->getMime();

            
            $header = $this->getHeaderByName('content-type', ContentType::class);
            $header->setType('multipart/mixed');
            $header->addParameter('boundary', $mime->boundary());
            return $this;
        }

        
        $parts = $this->body->getParts();
        if (! empty($parts)) {
            $part = array_shift($parts);
            $headers->addHeaders($part->getHeadersArray("\r\n"));
        }
        return $this;
    }

    
    public function getBody()
    {
        return $this->body;
    }

    
    public function getBodyText()
    {
        if ($this->body instanceof Mime\Message) {
            return $this->body->generateMessage(Headers::EOL);
        }

        return (string) $this->body;
    }

    
    protected function getHeaderByName($headerName, $headerClass)
    {
        $headers = $this->getHeaders();
        if ($headers->has($headerName)) {
            $header = $headers->get($headerName);
        } else {
            $header = new $headerClass();
            $headers->addHeader($header);
        }
        return $header;
    }

    
    protected function clearHeaderByName($headerName)
    {
        $this->getHeaders()->removeHeader($headerName);
    }

    
    protected function getAddressListFromHeader($headerName, $headerClass)
    {
        $header = $this->getHeaderByName($headerName, $headerClass);
        if (! $header instanceof Header\AbstractAddressList) {
            throw new Exception\DomainException(sprintf(
                'Cannot grab address list from header of type "%s"; not an AbstractAddressList implementation',
                $header::class
            ));
        }
        return $header->getAddressList();
    }

    
    protected function updateAddressList(AddressList $addressList, $emailOrAddressOrList, $name, $callingMethod)
    {
        if ($emailOrAddressOrList instanceof Traversable) {
            foreach ($emailOrAddressOrList as $address) {
                $addressList->add($address);
            }
            return;
        }
        if (is_array($emailOrAddressOrList)) {
            $addressList->addMany($emailOrAddressOrList);
            return;
        }
        if (! is_string($emailOrAddressOrList) && ! $emailOrAddressOrList instanceof Address\AddressInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string, AddressInterface, array, AddressList, or Traversable as its first argument;'
                . ' received "%s"',
                $callingMethod,
                is_object($emailOrAddressOrList) ? $emailOrAddressOrList::class : gettype($emailOrAddressOrList)
            ));
        }

        if (is_string($emailOrAddressOrList) && $name === null) {
            $addressList->addFromString($emailOrAddressOrList);
            return;
        }

        $addressList->add($emailOrAddressOrList, $name);
    }

    
    public function toString()
    {
        $headers = $this->getHeaders();
        return $headers->toString()
               . Headers::EOL
               . $this->getBodyText();
    }

    
    public static function fromString($rawMessage)
    {
        $message = new static();

        
        $headers = null;
        $content = null;
        Mime\Decode::splitMessage($rawMessage, $headers, $content, Headers::EOL);
        
            
        
        $message->setHeaders($headers);
        $message->setBody($content);
        return $message;
    }
}
