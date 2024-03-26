<?php


namespace Espo\Core\Duplicate;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use RuntimeException;

class WhereBuilderFactory
{
    public function __construct(private InjectableFactory $injectableFactory, private Metadata $metadata)
    {}

    public function has(string $entityType): bool
    {
        return (bool) $this->getClassName($entityType);
    }

    
    public function create(string $entityType): WhereBuilder
    {
        $className = $this->getClassName($entityType);

        if (!$className) {
            throw new RuntimeException("No duplicate-where-builder for '{$entityType}'.");
        }

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $entityType): ?string
    {
        
        return $this->metadata
            ->get(['recordDefs', $entityType, 'duplicateWhereBuilderClassName']);
    }
}
