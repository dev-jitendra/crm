<?php

namespace AsyncAws\S3\ValueObject;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\S3\Enum\BucketLocationConstraint;


final class CreateBucketConfiguration
{
    
    private $locationConstraint;

    
    public function __construct(array $input)
    {
        $this->locationConstraint = $input['LocationConstraint'] ?? null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    
    public function getLocationConstraint(): ?string
    {
        return $this->locationConstraint;
    }

    
    public function requestBody(\DomElement $node, \DomDocument $document): void
    {
        if (null !== $v = $this->locationConstraint) {
            if (!BucketLocationConstraint::exists($v)) {
                throw new InvalidArgument(sprintf('Invalid parameter "LocationConstraint" for "%s". The value "%s" is not a valid "BucketLocationConstraint".', __CLASS__, $v));
            }
            $node->appendChild($document->createElement('LocationConstraint', $v));
        }
    }
}
