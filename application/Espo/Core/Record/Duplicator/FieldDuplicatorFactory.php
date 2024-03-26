<?php


namespace Espo\Core\Record\Duplicator;

use Espo\ORM\Defs;
use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use RuntimeException;

class FieldDuplicatorFactory
{
    public function __construct(
        private Defs $defs,
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $entityType, string $field): FieldDuplicator
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("No field duplicator for the field.");
        }

        return $this->injectableFactory->create($className);
    }

    public function has(string $entityType, string $field): bool
    {
        return $this->getClassName($entityType, $field) !== null;
    }

    
    private function getClassName(string $entityType, string $field): ?string
    {
        $fieldDefs = $this->defs
            ->getEntity($entityType)
            ->getField($field);

        $className1 = $fieldDefs->getParam('duplicatorClassName');

        if ($className1) {
            
            return $className1;
        }

        $type = $fieldDefs->getType();

        $className2 = $this->metadata->get(['fields', $type, 'duplicatorClassName']);

        if ($className2) {
            
            return $className2;
        }

        return null;
    }
}
