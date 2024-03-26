<?php


namespace Espo\Core\Mail;

use Espo\Core\Mail\Message\Part;

interface Message
{
    
    public function hasHeader(string $name): bool;

    
    public function getHeader(string $attribute): ?string;

    
    public function getRawHeader(): string;

    
    public function getRawContent(): string;

    
    public function getFullRawContent(): string;

    
    public function getFlags(): array;

    
    public function isFetched(): bool;

    
    public function getPartList(): array;
}
