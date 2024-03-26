<?php

namespace AsyncAws\S3\ValueObject;


final class CompletedPart
{
    
    private $etag;

    
    private $partNumber;

    
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function getPartNumber(): ?int
    {
        return $this->partNumber;
    }

    
    public function requestBody(\DomElement $node, \DomDocument $document): void
    {
        if (null !== $v = $this->etag) {
            $node->appendChild($document->createElement('ETag', $v));
        }
        if (null !== $v = $this->partNumber) {
            $node->appendChild($document->createElement('PartNumber', $v));
        }
    }
}
