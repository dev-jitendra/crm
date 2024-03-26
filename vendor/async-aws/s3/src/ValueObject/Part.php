<?php

namespace AsyncAws\S3\ValueObject;


final class Part
{
    
    private $partNumber;

    
    private $lastModified;

    
    private $etag;

    
    private $size;

    
    public function __construct(array $input)
    {
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->etag = $input['ETag'] ?? null;
        $this->size = $input['Size'] ?? null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function getPartNumber(): ?int
    {
        return $this->partNumber;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }
}
