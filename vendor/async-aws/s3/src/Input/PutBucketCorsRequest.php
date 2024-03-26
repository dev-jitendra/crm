<?php

namespace AsyncAws\S3\Input;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Input;
use AsyncAws\Core\Request;
use AsyncAws\Core\Stream\StreamFactory;
use AsyncAws\S3\ValueObject\CORSConfiguration;

final class PutBucketCorsRequest extends Input
{
    
    private $bucket;

    
    private $corsConfiguration;

    
    private $contentMd5;

    
    private $expectedBucketOwner;

    
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->corsConfiguration = isset($input['CORSConfiguration']) ? CORSConfiguration::create($input['CORSConfiguration']) : null;
        $this->contentMd5 = $input['ContentMD5'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        parent::__construct($input);
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getBucket(): ?string
    {
        return $this->bucket;
    }

    public function getContentMd5(): ?string
    {
        return $this->contentMd5;
    }

    public function getCorsConfiguration(): ?CORSConfiguration
    {
        return $this->corsConfiguration;
    }

    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }

    
    public function request(): Request
    {
        
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->contentMd5) {
            $headers['Content-MD5'] = $this->contentMd5;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }

        
        $query = [];

        
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '?cors';

        

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';

        
        return new Request('PUT', $uriString, $query, $headers, StreamFactory::create($body));
    }

    public function setBucket(?string $value): self
    {
        $this->bucket = $value;

        return $this;
    }

    public function setContentMd5(?string $value): self
    {
        $this->contentMd5 = $value;

        return $this;
    }

    public function setCorsConfiguration(?CORSConfiguration $value): self
    {
        $this->corsConfiguration = $value;

        return $this;
    }

    public function setExpectedBucketOwner(?string $value): self
    {
        $this->expectedBucketOwner = $value;

        return $this;
    }

    private function requestBody(\DomNode $node, \DomDocument $document): void
    {
        if (null === $v = $this->corsConfiguration) {
            throw new InvalidArgument(sprintf('Missing parameter "CORSConfiguration" for "%s". The value cannot be null.', __CLASS__));
        }

        $node->appendChild($child = $document->createElement('CORSConfiguration'));
        $child->setAttribute('xmlns', 'http:
        $v->requestBody($child, $document);
    }
}
