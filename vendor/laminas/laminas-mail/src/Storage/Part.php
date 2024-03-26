<?php

namespace Laminas\Mail\Storage;

use ArrayIterator;
use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Headers;
use Laminas\Mime;
use Laminas\Mime\Exception\RuntimeException;
use RecursiveIterator;
use ReturnTypeWillChange;
use Stringable;

use function array_map;
use function count;
use function current;
use function implode;
use function is_array;
use function iterator_to_array;
use function preg_replace;
use function stripos;
use function strlen;
use function strtolower;
use function trim;

class Part implements RecursiveIterator, Part\PartInterface, Stringable
{
    
    protected $headers;

    
    protected $content;

    
    protected $topLines = '';

    
    protected $parts = [];

    
    protected $countParts;

    
    protected $iterationPos = 1;

    
    protected $mail;

    
    protected $messageNum = 0;

    
    public function __construct(array $params)
    {
        if (isset($params['handler'])) {
            if (! $params['handler'] instanceof AbstractStorage) {
                throw new Exception\InvalidArgumentException('handler is not a valid mail handler');
            }
            if (! isset($params['id'])) {
                throw new Exception\InvalidArgumentException('need a message id with a handler');
            }

            $this->mail       = $params['handler'];
            $this->messageNum = $params['id'];
        }

        $params['strict'] ??= false;

        if (isset($params['raw'])) {
            Mime\Decode::splitMessage(
                $params['raw'],
                $this->headers,
                $this->content,
                Mime\Mime::LINEEND,
                $params['strict']
            );
        } elseif (isset($params['headers'])) {
            if (is_array($params['headers'])) {
                $this->headers = new Headers();
                $this->headers->addHeaders($params['headers']);
            } else {
                if (empty($params['noToplines'])) {
                    Mime\Decode::splitMessage($params['headers'], $this->headers, $this->topLines);
                } else {
                    $this->headers = Headers::fromString($params['headers']);
                }
            }

            if (isset($params['content'])) {
                $this->content = $params['content'];
            }
        }
    }

    
    public function isMultipart()
    {
        try {
            return stripos($this->contentType, 'multipart/') === 0;
        } catch (Exception\ExceptionInterface) {
            return false;
        }
    }

    
    public function getContent()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        if ($this->mail) {
            return $this->mail->getRawContent($this->messageNum);
        }

        throw new Exception\RuntimeException('no content');
    }

    
    public function getSize()
    {
        return strlen($this->getContent());
    }

    
    protected function cacheContent()
    {
        
        if ($this->content === null && $this->mail) {
            $this->content = $this->mail->getRawContent($this->messageNum);
        }

        if (! $this->isMultipart()) {
            return;
        }

        
        $boundary = $this->getHeaderField('content-type', 'boundary');
        if (! $boundary) {
            throw new Exception\RuntimeException('no boundary found in content type to split message');
        }
        $parts = Mime\Decode::splitMessageStruct($this->content, $boundary);
        if ($parts === null) {
            return;
        }
        $counter = 1;
        foreach ($parts as $part) {
            $this->parts[$counter++] = new static(['headers' => $part['header'], 'content' => $part['body']]);
        }
    }

    
    public function getPart($num)
    {
        if (isset($this->parts[$num])) {
            return $this->parts[$num];
        }

        if (! $this->mail && $this->content === null) {
            throw new Exception\RuntimeException('part not found');
        }

        
            
            
        

        $this->cacheContent();

        if (! isset($this->parts[$num])) {
            throw new Exception\RuntimeException('part not found');
        }

        return $this->parts[$num];
    }

    
    public function countParts()
    {
        if ($this->countParts) {
            return $this->countParts;
        }

        $this->countParts = count($this->parts);
        if ($this->countParts) {
            return $this->countParts;
        }

        
            
            
        

        $this->cacheContent();

        $this->countParts = count($this->parts);
        return $this->countParts;
    }

    
    public function getHeaders()
    {
        if (null === $this->headers) {
            if ($this->mail) {
                $part          = $this->mail->getRawHeader($this->messageNum);
                $this->headers = Headers::fromString($part);
            } else {
                $this->headers = new Headers();
            }
        }
        if (! $this->headers instanceof Headers) {
            throw new Exception\RuntimeException(
                '$this->headers must be an instance of Headers'
            );
        }

        return $this->headers;
    }

    
    public function getHeader($name, $format = null)
    {
        $header = $this->getHeaders()->get($name);
        if ($header === false) {
            $lowerName = strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $name));
            $header    = $this->getHeaders()->get($lowerName);
            if ($header === false) {
                throw new Exception\InvalidArgumentException(
                    "Header with Name $name or $lowerName not found"
                );
            }
        }

        switch ($format) {
            case 'string':
                if ($header instanceof HeaderInterface) {
                    $return = $header->getFieldValue(HeaderInterface::FORMAT_RAW);
                } else {
                    $return = trim(implode(
                        Mime\Mime::LINEEND,
                        array_map(static fn($header): string
                            => $header->getFieldValue(HeaderInterface::FORMAT_RAW), iterator_to_array($header))
                    ), Mime\Mime::LINEEND);
                }
                break;
            case 'array':
                if ($header instanceof HeaderInterface) {
                    $return = [$header->getFieldValue()];
                } else {
                    $return = [];
                    foreach ($header as $h) {
                        $return[] = $h->getFieldValue(HeaderInterface::FORMAT_RAW);
                    }
                }
                break;
            default:
                $return = $header;
        }

        return $return;
    }

    
    public function getHeaderField($name, $wantedPart = '0', $firstName = '0')
    {
        return Mime\Decode::splitHeaderField(current($this->getHeader($name, 'array')), $wantedPart, $firstName);
    }

    
    public function __get($name)
    {
        return $this->getHeader($name, 'string');
    }

    
    public function __isset($name)
    {
        return $this->getHeaders()->has($name);
    }

    
    public function __toString(): string
    {
        return $this->getContent();
    }

    
    #[ReturnTypeWillChange]
    public function hasChildren()
    {
        $current = $this->current();
        return $current && $current instanceof self && $current->isMultipart();
    }

    
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        return $this->current();
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        if ($this->countParts === null) {
            $this->countParts();
        }
        return $this->iterationPos && $this->iterationPos <= $this->countParts;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->iterationPos;
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->iterationPos;
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->getPart($this->iterationPos);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->countParts();
        $this->iterationPos = 1;
    }
}
