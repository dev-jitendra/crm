<?php

namespace AsyncAws\S3\ValueObject;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\S3\Enum\Event;


final class TopicConfiguration
{
    private $id;

    
    private $topicArn;

    
    private $events;

    private $filter;

    
    public function __construct(array $input)
    {
        $this->id = $input['Id'] ?? null;
        $this->topicArn = $input['TopicArn'] ?? null;
        $this->events = $input['Events'] ?? null;
        $this->filter = isset($input['Filter']) ? NotificationConfigurationFilter::create($input['Filter']) : null;
    }

    public static function create($input): self
    {
        return $input instanceof self ? $input : new self($input);
    }

    
    public function getEvents(): array
    {
        return $this->events ?? [];
    }

    public function getFilter(): ?NotificationConfigurationFilter
    {
        return $this->filter;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTopicArn(): string
    {
        return $this->topicArn;
    }

    
    public function requestBody(\DomElement $node, \DomDocument $document): void
    {
        if (null !== $v = $this->id) {
            $node->appendChild($document->createElement('Id', $v));
        }
        if (null === $v = $this->topicArn) {
            throw new InvalidArgument(sprintf('Missing parameter "TopicArn" for "%s". The value cannot be null.', __CLASS__));
        }
        $node->appendChild($document->createElement('Topic', $v));
        if (null === $v = $this->events) {
            throw new InvalidArgument(sprintf('Missing parameter "Events" for "%s". The value cannot be null.', __CLASS__));
        }
        foreach ($v as $item) {
            if (!Event::exists($item)) {
                throw new InvalidArgument(sprintf('Invalid parameter "Event" for "%s". The value "%s" is not a valid "Event".', __CLASS__, $item));
            }
            $node->appendChild($document->createElement('Event', $item));
        }

        if (null !== $v = $this->filter) {
            $node->appendChild($child = $document->createElement('Filter'));

            $v->requestBody($child, $document);
        }
    }
}
