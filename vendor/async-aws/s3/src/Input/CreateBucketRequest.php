<?php

namespace AsyncAws\S3\Input;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Input;
use AsyncAws\Core\Request;
use AsyncAws\Core\Stream\StreamFactory;
use AsyncAws\S3\Enum\BucketCannedACL;
use AsyncAws\S3\ValueObject\CreateBucketConfiguration;

final class CreateBucketRequest extends Input
{
    
    private $acl;

    
    private $bucket;

    
    private $createBucketConfiguration;

    
    private $grantFullControl;

    
    private $grantRead;

    
    private $grantReadAcp;

    
    private $grantWrite;

    
    private $grantWriteAcp;

    
    private $objectLockEnabledForBucket;

    
    public function __construct(array $input = [])
    {
        $this->acl = $input['ACL'] ?? null;
        $this->bucket = $input['Bucket'] ?? null;
        $this->createBucketConfiguration = isset($input['CreateBucketConfiguration']) ? CreateBucketConfiguration::create($input['CreateBucketConfiguration']) : null;
        $this->grantFullControl = $input['GrantFullControl'] ?? null;
        $this->grantRead = $input['GrantRead'] ?? null;
        $this->grantReadAcp = $input['GrantReadACP'] ?? null;
        $this->grantWrite = $input['GrantWrite'] ?? null;
        $this->grantWriteAcp = $input['GrantWriteACP'] ?? null;
        $this->objectLockEnabledForBucket = $input['ObjectLockEnabledForBucket'] ?? null;
        parent::__construct($input);
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    
    public function getAcl(): ?string
    {
        return $this->acl;
    }

    public function getBucket(): ?string
    {
        return $this->bucket;
    }

    public function getCreateBucketConfiguration(): ?CreateBucketConfiguration
    {
        return $this->createBucketConfiguration;
    }

    public function getGrantFullControl(): ?string
    {
        return $this->grantFullControl;
    }

    public function getGrantRead(): ?string
    {
        return $this->grantRead;
    }

    public function getGrantReadAcp(): ?string
    {
        return $this->grantReadAcp;
    }

    public function getGrantWrite(): ?string
    {
        return $this->grantWrite;
    }

    public function getGrantWriteAcp(): ?string
    {
        return $this->grantWriteAcp;
    }

    public function getObjectLockEnabledForBucket(): ?bool
    {
        return $this->objectLockEnabledForBucket;
    }

    
    public function request(): Request
    {
        
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->acl) {
            if (!BucketCannedACL::exists($this->acl)) {
                throw new InvalidArgument(sprintf('Invalid parameter "ACL" for "%s". The value "%s" is not a valid "BucketCannedACL".', __CLASS__, $this->acl));
            }
            $headers['x-amz-acl'] = $this->acl;
        }
        if (null !== $this->grantFullControl) {
            $headers['x-amz-grant-full-control'] = $this->grantFullControl;
        }
        if (null !== $this->grantRead) {
            $headers['x-amz-grant-read'] = $this->grantRead;
        }
        if (null !== $this->grantReadAcp) {
            $headers['x-amz-grant-read-acp'] = $this->grantReadAcp;
        }
        if (null !== $this->grantWrite) {
            $headers['x-amz-grant-write'] = $this->grantWrite;
        }
        if (null !== $this->grantWriteAcp) {
            $headers['x-amz-grant-write-acp'] = $this->grantWriteAcp;
        }
        if (null !== $this->objectLockEnabledForBucket) {
            $headers['x-amz-bucket-object-lock-enabled'] = $this->objectLockEnabledForBucket ? 'true' : 'false';
        }

        
        $query = [];

        
        $uri = [];
        if (null === $v = $this->bucket) {
            throw new InvalidArgument(sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . rawurlencode($uri['Bucket']);

        

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';

        
        return new Request('PUT', $uriString, $query, $headers, StreamFactory::create($body));
    }

    
    public function setAcl(?string $value): self
    {
        $this->acl = $value;

        return $this;
    }

    public function setBucket(?string $value): self
    {
        $this->bucket = $value;

        return $this;
    }

    public function setCreateBucketConfiguration(?CreateBucketConfiguration $value): self
    {
        $this->createBucketConfiguration = $value;

        return $this;
    }

    public function setGrantFullControl(?string $value): self
    {
        $this->grantFullControl = $value;

        return $this;
    }

    public function setGrantRead(?string $value): self
    {
        $this->grantRead = $value;

        return $this;
    }

    public function setGrantReadAcp(?string $value): self
    {
        $this->grantReadAcp = $value;

        return $this;
    }

    public function setGrantWrite(?string $value): self
    {
        $this->grantWrite = $value;

        return $this;
    }

    public function setGrantWriteAcp(?string $value): self
    {
        $this->grantWriteAcp = $value;

        return $this;
    }

    public function setObjectLockEnabledForBucket(?bool $value): self
    {
        $this->objectLockEnabledForBucket = $value;

        return $this;
    }

    private function requestBody(\DomNode $node, \DomDocument $document): void
    {
        if (null !== $v = $this->createBucketConfiguration) {
            $node->appendChild($child = $document->createElement('CreateBucketConfiguration'));
            $child->setAttribute('xmlns', 'http:
            $v->requestBody($child, $document);
        }
    }
}
