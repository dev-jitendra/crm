<?php

declare(strict_types=1);

namespace League\Flysystem;

use Generator;
use IteratorAggregate;
use Traversable;


class DirectoryListing implements IteratorAggregate
{
    
    private $listing;

    
    public function __construct(iterable $listing)
    {
        $this->listing = $listing;
    }

    public function filter(callable $filter): DirectoryListing
    {
        $generator = (static function (iterable $listing) use ($filter): Generator {
            foreach ($listing as $item) {
                if ($filter($item)) {
                    yield $item;
                }
            }
        })($this->listing);

        return new DirectoryListing($generator);
    }

    public function map(callable $mapper): DirectoryListing
    {
        $generator = (static function (iterable $listing) use ($mapper): Generator {
            foreach ($listing as $item) {
                yield $mapper($item);
            }
        })($this->listing);

        return new DirectoryListing($generator);
    }

    
    public function getIterator(): iterable
    {
        return $this->listing;
    }

    
    public function toArray(): array
    {
        return $this->listing instanceof Traversable
            ? iterator_to_array($this->listing, false)
            : (array) $this->listing;
    }
}
