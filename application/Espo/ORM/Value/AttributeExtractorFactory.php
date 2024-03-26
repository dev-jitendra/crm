<?php


namespace Espo\ORM\Value;


interface AttributeExtractorFactory
{
    
    public function create(string $entityType, string $field): AttributeExtractor;
}
