<?php


namespace Espo\ORM\Locker;


interface Locker
{
    
    public function isLocked(): bool;

    
    public function lockExclusive(string $entityType): void;

    
    public function lockShare(string $entityType): void;

    
    public function commit(): void;

    
    public function rollback(): void;
}
