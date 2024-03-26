<?php


namespace Espo\Core\FieldValidation;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\FieldUtil;

use Espo\ORM\Entity;
use RuntimeException;

class ValidatorFactory
{

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata,
        private FieldUtil $fieldUtil
    ) {}

    public function isCreatable(string $entityType, string $field, string $type): bool
    {
        return $this->getClassName($entityType, $field, $type) !== null;
    }

    
    public function create(string $entityType, string $field, string $type): Validator
    {
        $className = $this->getClassName($entityType, $field, $type);

        if (!$className) {
            throw new RuntimeException("No validator.");
        }

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $entityType, string $field, string $type): ?string
    {
        
        $fieldType = $this->fieldUtil->getEntityTypeFieldParam($entityType, $field, 'type');

        return
            $this->metadata->get(['entityDefs', $entityType, 'fields', $field, 'validatorClassNameMap', $type]) ??
            $this->metadata->get(['fields', $fieldType ?? '', 'validatorClassNameMap', $type]);
    }

    
    public function createAdditionalList(string $entityType, string $field): array
    {
        
        $classNameList = $this->metadata
            ->get(['entityDefs', $entityType, 'fields', $field, 'validatorClassNameList']) ?? [];

        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
