<?php

namespace ZBateson\MailMimeParser\Message\Part;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Message\PartFilter;
use ZBateson\MailMimeParser\Stream\StreamFactory;


abstract class ParentPart extends MessagePart
{
    
    protected $partFilterFactory;

    
    protected $children = [];

    
    public function __construct(
        PartStreamFilterManager $partStreamFilterManager,
        StreamFactory $streamFactory,
        PartFilterFactory $partFilterFactory,
        PartBuilder $partBuilder,
        StreamInterface $stream = null,
        StreamInterface $contentStream = null
    ) {
        parent::__construct($partStreamFilterManager, $streamFactory, $stream, $contentStream);
        $this->partFilterFactory = $partFilterFactory;

        $pbChildren = $partBuilder->getChildren();
        if (!empty($pbChildren)) {
            $this->children = array_map(function ($child) use ($stream) {
                $childPart = $child->createMessagePart($stream);
                $childPart->parent = $this;
                return $childPart;
            }, $pbChildren);
        }
    }

    
    protected function getAllNonFilteredParts()
    {
        $parts = [ $this ];
        foreach ($this->children as $part) {
            if ($part instanceof MimePart) {
                $parts = array_merge(
                    $parts,
                    $part->getAllNonFilteredParts()
                );
            } else {
                array_push($parts, $part);
            }
        }
        return $parts;
    }

    
    public function getPart($index, PartFilter $filter = null)
    {
        $parts = $this->getAllParts($filter);
        if (!isset($parts[$index])) {
            return null;
        }
        return $parts[$index];
    }

    
    public function getAllParts(PartFilter $filter = null)
    {
        $parts = $this->getAllNonFilteredParts();
        if (!empty($filter)) {
            return array_values(array_filter(
                $parts,
                [ $filter, 'filter' ]
            ));
        }
        return $parts;
    }

    
    public function getPartCount(PartFilter $filter = null)
    {
        return count($this->getAllParts($filter));
    }

    
    public function getChild($index, PartFilter $filter = null)
    {
        $parts = $this->getChildParts($filter);
        if (!isset($parts[$index])) {
            return null;
        }
        return $parts[$index];
    }

    
    public function getChildParts(PartFilter $filter = null)
    {
        if ($filter !== null) {
            return array_values(array_filter($this->children, [ $filter, 'filter' ]));
        }
        return $this->children;
    }

    
    public function getChildCount(PartFilter $filter = null)
    {
        return count($this->getChildParts($filter));
    }

    
    public function getPartByMimeType($mimeType, $index = 0)
    {
        $partFilter = $this->partFilterFactory->newFilterFromContentType($mimeType);
        return $this->getPart($index, $partFilter);
    }

    
    public function getAllPartsByMimeType($mimeType)
    {
        $partFilter = $this->partFilterFactory->newFilterFromContentType($mimeType);
        return $this->getAllParts($partFilter);
    }

    
    public function getCountOfPartsByMimeType($mimeType)
    {
        $partFilter = $this->partFilterFactory->newFilterFromContentType($mimeType);
        return $this->getPartCount($partFilter);
    }

    
    public function addChild(MessagePart $part, $position = null)
    {
        if ($part !== $this) {
            $part->parent = $this;
            array_splice(
                $this->children,
                ($position === null) ? count($this->children) : $position,
                0,
                [ $part ]
            );
            $this->onChange();
        }
    }

    
    public function removePart(MessagePart $part)
    {
        $parent = $part->getParent();
        if ($this !== $parent && $parent !== null) {
            return $parent->removePart($part);
        } else {
            $position = array_search($part, $this->children, true);
            if ($position !== false && is_int($position)) {
                array_splice($this->children, $position, 1);
                $this->onChange();
                return $position;
            }
        }
        return null;
    }

    
    public function removeAllParts(PartFilter $filter = null)
    {
        foreach ($this->getAllParts($filter) as $part) {
            if ($part === $this) {
                continue;
            }
            $this->removePart($part);
        }
    }
}
