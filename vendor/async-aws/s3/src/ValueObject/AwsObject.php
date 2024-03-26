<?php

namespace AsyncAws\S3\ValueObject;

use AsyncAws\S3\Enum\ObjectStorageClass;


final class AwsObject
{
    
    private $key;

    
    private $lastModified;

    
    private $etag;

    
    private $size;

    
    private $storageClass;

    
    private $owner;

    
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->etag = $input['ETag'] ?? null;
        $this->size = $input['Size'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    
    public function getStorageClass(): ?string
    {
        return $this->storageClass;
    }
}
