<?php

namespace ZBateson\MailMimeParser\Message;

use ZBateson\MailMimeParser\Message\Part\MessagePart;
use ZBateson\MailMimeParser\Message\Part\MimePart;
use InvalidArgumentException;


class PartFilter
{
    
    const FILTER_OFF = 0;
    
    
    const FILTER_EXCLUDE = 1;
    
    
    const FILTER_INCLUDE = 2;

    
    private $hascontent = PartFilter::FILTER_OFF;

    
    private $multipart = PartFilter::FILTER_OFF;
    
    
    private $textpart = PartFilter::FILTER_OFF;
    
    
    private $signedpart = PartFilter::FILTER_EXCLUDE;
    
    
    private $hashCode;
    
    
    private $headers = [];
    
    
    public static function fromContentType($mimeType)
    {
        return new static([
            'headers' => [
                static::FILTER_INCLUDE => [
                    'Content-Type' => $mimeType
                ]
            ]
        ]);
    }
    
    
    public static function fromInlineContentType($mimeType)
    {
        return new static([
            'headers' => [
                static::FILTER_INCLUDE => [
                    'Content-Type' => $mimeType
                ],
                static::FILTER_EXCLUDE => [
                    'Content-Disposition' => 'attachment'
                ]
            ]
        ]);
    }
    
    
    public static function fromDisposition($disposition, $multipart = PartFilter::FILTER_OFF)
    {
        return new static([
            'multipart' => $multipart,
            'headers' => [
                static::FILTER_INCLUDE => [
                    'Content-Disposition' => $disposition
                ]
            ]
        ]);
    }
    
    
    public function __construct(array $filter = [])
    {
        $params = [ 'hascontent', 'multipart', 'textpart', 'signedpart', 'headers' ];
        foreach ($params as $param) {
            if (isset($filter[$param])) {
                $this->__set($param, $filter[$param]);
            }
        }
    }
    
    
    private function validateArgument($name, $value, array $valid)
    {
        if (!in_array($value, $valid)) {
            $last = array_pop($valid);
            throw new InvalidArgumentException(
                '$value parameter for ' . $name . ' must be one of '
                . join(', ', $valid) . ' or ' . $last . ' - "' . $value
                . '" provided'
            );
        }
    }
    
    
    public function setHeaders(array $headers)
    {
        array_walk($headers, function ($v, $k) {
            $this->validateArgument(
                'headers',
                $k,
                [ static::FILTER_EXCLUDE, static::FILTER_INCLUDE ]
            );
            if (!is_array($v)) {
                throw new InvalidArgumentException(
                    '$value must be an array with keys set to FILTER_EXCLUDE, '
                    . 'FILTER_INCLUDE and values set to an array of header '
                    . 'name => values'
                );
            }
        });
        $this->headers = $headers;
    }
    
    
    public function __set($name, $value)
    {
        if ($name === 'hascontent' || $name === 'multipart'
            || $name === 'textpart' || $name === 'signedpart') {
            if (is_array($value)) {
                throw new InvalidArgumentException('$value must be not be an array');
            }
            $this->validateArgument(
                $name,
                $value,
                [ static::FILTER_OFF, static::FILTER_EXCLUDE, static::FILTER_INCLUDE ]
            );
            $this->$name = $value;
        } elseif ($name === 'headers') {
            if (!is_array($value)) {
                throw new InvalidArgumentException('$value must be an array');
            }
            $this->setHeaders($value);
        }
    }
    
    
    public function __isset($name)
    {
        return isset($this->$name);
    }
    
    
    public function __get($name)
    {
        return $this->$name;
    }

    
    private function failsHasContentFilter(MessagePart $part)
    {
        return ($this->hascontent === static::FILTER_EXCLUDE && $part->hasContent())
            || ($this->hascontent === static::FILTER_INCLUDE && !$part->hasContent());
    }
    
    
    private function failsMultiPartFilter(MessagePart $part)
    {
        if (!($part instanceof MimePart)) {
            return $this->multipart !== static::FILTER_EXCLUDE;
        }
        return ($this->multipart === static::FILTER_EXCLUDE && $part->isMultiPart())
            || ($this->multipart === static::FILTER_INCLUDE && !$part->isMultiPart());
    }
    
    
    private function failsTextPartFilter(MessagePart $part)
    {
        return ($this->textpart === static::FILTER_EXCLUDE && $part->isTextPart())
            || ($this->textpart === static::FILTER_INCLUDE && !$part->isTextPart());
    }
    
    
    private function failsSignedPartFilter(MessagePart $part)
    {
        if ($this->signedpart === static::FILTER_OFF) {
            return false;
        } elseif (!$part->isMime() || $part->getParent() === null) {
            return ($this->signedpart === static::FILTER_INCLUDE);
        }
        $partMimeType = $part->getContentType();
        $parentMimeType = $part->getParent()->getContentType();
        $parentProtocol = $part->getParent()->getHeaderParameter('Content-Type', 'protocol');
        if (strcasecmp($parentMimeType, 'multipart/signed') === 0 && strcasecmp($partMimeType, $parentProtocol) === 0) {
            return ($this->signedpart === static::FILTER_EXCLUDE);
        }
        return ($this->signedpart === static::FILTER_INCLUDE);
    }
    
    
    private function failsHeaderFor(MessagePart $part, $type, $name, $header)
    {
        $headerValue = null;
        
        static $map = [
            'content-type' => 'getContentType',
            'content-disposition' => 'getContentDisposition',
            'content-transfer-encoding' => 'getContentTransferEncoding',
            'content-id' => 'getContentId'
        ];
        $lower = strtolower($name);
        if (isset($map[$lower])) {
            $headerValue = call_user_func([$part, $map[$lower]]);
        } elseif (!($part instanceof MimePart)) {
            return ($type === static::FILTER_INCLUDE);
        } else {
            $headerValue = $part->getHeaderValue($name);
        }
        
        return (($type === static::FILTER_EXCLUDE && strcasecmp($headerValue, $header) === 0)
            || ($type === static::FILTER_INCLUDE && strcasecmp($headerValue, $header) !== 0));
    }
    
    
    private function failsHeaderPartFilter(MessagePart $part)
    {
        foreach ($this->headers as $type => $values) {
            foreach ($values as $name => $header) {
                if ($this->failsHeaderFor($part, $type, $name, $header)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    
    public function filter(MessagePart $part)
    {
        return !($this->failsMultiPartFilter($part)
            || $this->failsTextPartFilter($part)
            || $this->failsSignedPartFilter($part)
            || $this->failsHeaderPartFilter($part));
    }
}
