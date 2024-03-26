<?php

namespace AsyncAws\Core\Stream;


interface RequestStream extends \IteratorAggregate
{
    
    public function length(): ?int;

    public function stringify(): string;

    public function hash(string $algo = 'sha256', bool $raw = false): string;
}
