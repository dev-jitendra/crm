<?php

namespace AsyncAws\S3\ValueObject;


final class CopyObjectResult
{
    
    private $etag;

    
    private $lastModified;

    
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
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
}
