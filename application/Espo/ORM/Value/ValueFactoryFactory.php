<?php


namespace Espo\ORM\Value;

interface ValueFactoryFactory
{
    
    public function isCreatable(string $entityType, string $field): bool;

    
    public function create(string $entityType, string $field): ValueFactory;
}
