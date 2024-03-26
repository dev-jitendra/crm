<?php


namespace Espo\ORM\Value;

use stdClass;

class GeneralAttributeExtractor
{
    
    private AttributeExtractorFactory $factory;

    
    private $cache = [];

    
    public function __construct(AttributeExtractorFactory $factory)
    {
        $this->factory = $factory;
    }

    
    public function extract(string $entityType, string $field, ?object $value): stdClass
    {
        $extractor = $this->getExtractor($entityType, $field);

        if (is_null($value)) {
            return $extractor->extractFromNull($field);
        }

        return $extractor->extract($value, $field);
    }

    
    private function getExtractor(string $entityType, string $field): AttributeExtractor
    {
        $key = $entityType . '_' . $field;

        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->factory->create($entityType, $field);
        }

        return $this->cache[$key];
    }
}
