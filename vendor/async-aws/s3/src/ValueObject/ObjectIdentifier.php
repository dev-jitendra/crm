<?php

namespace AsyncAws\S3\ValueObject;

use AsyncAws\Core\Exception\InvalidArgument;


final class ObjectIdentifier
{
    
    private $key;

    
    private $versionId;

    
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getVersionId(): ?string
    {
        return $this->versionId;
    }

    
    public function requestBody(\DomElement $node, \DomDocument $document): void
    {
        if (null === $v = $this->key) {
            throw new InvalidArgument(sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $node->appendChild($document->createElement('Key', $v));
        if (null !== $v = $this->versionId) {
            $node->appendChild($document->createElement('VersionId', $v));
        }
    }
}
