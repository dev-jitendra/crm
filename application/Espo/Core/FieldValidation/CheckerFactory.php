<?php


namespace Espo\Core\FieldValidation;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use RuntimeException;

class CheckerFactory
{
    
    private $classNameCache = [];

    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function isCreatable(string $entityType, string $field): bool
    {
        return (bool) $this->getClassName($entityType, $field);
    }

    
    public function create(string $entityType, string $field): object
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("Validator for '{$entityType}.{$field}' does not exist.");
        }

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $entityType, string $field): ?string
    {
        $key = $entityType . '_' . $field;

        if (!array_key_exists($key, $this->classNameCache)) {
            $this->classNameCache[$key] = $this->getClassNameNoCache($entityType, $field);
        }

        return $this->classNameCache[$key];
    }

    
    private function getClassNameNoCache(string $entityType, string $field): ?string
    {
        $className1 = $this->metadata
            ->get(['entityDefs', $entityType, 'fields', $field, 'validatorClassName']);

        if ($className1) {
            return $className1;
        }

        $fieldType = $this->metadata
            ->get(['entityDefs', $entityType, 'fields', $field, 'type']);

        $className2 = $this->metadata
            ->get(['fields', $fieldType, 'validatorClassName']);

        if ($className2) {
            return $className2;
        }

        $className3 = 'Espo\\Classes\\FieldValidators\\' . ucfirst($fieldType) . 'Type';

        if (class_exists($className3)) {
            return $className3;
        }

        return null;
    }
}
