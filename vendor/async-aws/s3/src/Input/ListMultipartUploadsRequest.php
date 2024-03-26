<?php

namespace AsyncAws\S3\Input;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Input;
use AsyncAws\Core\Request;
use AsyncAws\Core\Stream\StreamFactory;
use AsyncAws\S3\Enum\EncodingType;

final class ListMultipartUploadsRequest extends Input
{
    
    private $bucket;

    
    private $delimiter;

    
    private $encodingType;

    
    private $keyMarker;

    
    private $maxUploads;

    
    private $prefix;

    
    private $uploadIdMarker;

    
    private $expectedBucketOwner;

    
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->delimiter = $input['Delimiter'] ?? null;
        $this->encodingType = $input['EncodingType'] ?? null;
        $this->keyMarker = $input['KeyMarker'] ?? null;
        $this->maxUploads = $input['MaxUploads'] ?? null;
        $this->prefix = $input['Prefix'] ?? null;
        $this->uploadIdMarker = $input['UploadIdMarker'] ?? null;
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

    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    
    public function getEncodingType(): ?string
    {
        return $this->encodingType;
    }

    public function getExpectedBucketOwner(): ?string
    {
        return $this->expectedBucketOwner;
    }

    public function getKeyMarker(): ?string
    {
        return $this->keyMarker;
    }

    public function getMaxUploads(): ?int
    {
        return $this->maxUploads;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getUploadIdMarker(): ?string
    {
        return $this->uploadIdMarker;
    }

    
    public function request(): Request
    {
        
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }

        
        $query = [];
        if (null !== $this->delimiter) {
            $query['delimiter'] = $this->delimiter;
        }
        if (null !== $this->encodingType) {
            if (!EncodingType::exists($this->encodingType)) {
                throw new InvalidArgument(sprintf('Invalid parameter "EncodingType" for "%s". The value "%s" is not a valid "EncodingType".', __CLASS__, $this->encodingType));
            }
            $query['encoding-type'] = $this->encodingType;
        }
        if (null !== $this->keyMarker) {
            $query['key-marker'] = $this->keyMarker;
        }
        if (null !== $this->maxUploads) {
            $query['max-uploads'] = (string) $this->maxUploads;
        }
        if (null !== $this->prefix) {
            $query['prefix'] = $this->prefix;
        }
        if (null !== $this->uploadIdMarker) {
            $query['upload-id-marker'] = $this->uploadIdMarker;
        }

        
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']) . '?uploads';

        
        $body = '';

        
        return new Request('GET', $uriString, $query, $headers, StreamFactory::create($body));
    }

    public function setBucket(?string $value): self
    {
        $this->bucket = $value;

        return $this;
    }

    public function setDelimiter(?string $value): self
    {
        $this->delimiter = $value;

        return $this;
    }

    
    public function setEncodingType(?string $value): self
    {
        $this->encodingType = $value;

        return $this;
    }

    public function setExpectedBucketOwner(?string $value): self
    {
        $this->expectedBucketOwner = $value;

        return $this;
    }

    public function setKeyMarker(?string $value): self
    {
        $this->keyMarker = $value;

        return $this;
    }

    public function setMaxUploads(?int $value): self
    {
        $this->maxUploads = $value;

        return $this;
    }

    public function setPrefix(?string $value): self
    {
        $this->prefix = $value;

        return $this;
    }

    public function setUploadIdMarker(?string $value): self
    {
        $this->uploadIdMarker = $value;

        return $this;
    }
}
