<?php


namespace Espo\Core\ORM;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Metadata as OrmMetadata;
use Espo\ORM\Value\ValueFactory;
use Espo\ORM\Value\ValueFactoryFactory as ValueFactoryFactoryInteface;

use RuntimeException;

class ValueFactoryFactory implements ValueFactoryFactoryInteface
{
    public function __construct(
        private Metadata $metadata,
        private OrmMetadata $ormMetadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function isCreatable(string $entityType, string $field): bool
    {
        return $this->getClassName($entityType, $field) !== null;
    }

    public function create(string $entityType, string $field): ValueFactory
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("Could not get ValueFactory for '{$entityType}.{$field}'.");
        }

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $entityType, string $field): ?string
    {
        $fieldDefs = $this->ormMetadata
            ->getDefs()
            ->getEntity($entityType)
            ->getField($field);

        
        $className = $fieldDefs->getParam('valueFactoryClassName');

        if ($className) {
            return $className;
        }

        $type = $fieldDefs->getType();

        
        return $this->metadata->get(['fields', $type, 'valueFactoryClassName']);
    }
}
