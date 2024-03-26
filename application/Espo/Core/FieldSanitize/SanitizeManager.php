<?php


namespace Espo\Core\FieldSanitize;

use Espo\Core\FieldSanitize\Sanitizer\Data;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\FieldUtil;
use Espo\Core\Utils\Metadata;
use stdClass;

class SanitizeManager
{
    public function __construct(
        private Metadata $metadata,
        private FieldUtil $fieldUtil,
        private InjectableFactory $injectableFactory
    ) {}

    public function process(string $entityType, stdClass $rawData): void
    {
        $data = new Data($rawData);

        foreach ($this->fieldUtil->getEntityTypeFieldList($entityType) as $field) {
            if (!$this->isFieldSetInData($entityType, $field, $rawData)) {
                continue;
            }

            $this->processField($entityType, $field, $data);
        }
    }

    private function processField(string $entityType, string $field, Data $data): void
    {
        foreach ($this->getSanitizerList($entityType, $field) as $sanitizer) {
            $sanitizer->sanitize($data, $field);
        }
    }

    private function isFieldSetInData(string $entityType, string $field, stdClass $data): bool
    {
        $attributeList = $this->fieldUtil->getActualAttributeList($entityType, $field);

        $isSet = false;

        foreach ($attributeList as $attribute) {
            if (property_exists($data, $attribute)) {
                $isSet = true;

                break;
            }
        }

        return $isSet;
    }

    
    private function getSanitizerList(string $entityType, string $field): array
    {
        $fieldType = $this->fieldUtil->getFieldType($entityType, $field);

        if (!$fieldType) {
            return [];
        }

        
        $className = $this->metadata->get("fields.$fieldType.sanitizerClassName");

        if ($className) {
            $classNameList[] = $className;
        }

        
        $classNameList = $this->metadata->get("entityDefs.$entityType.fields.$field.sanitizerClassNameList") ?? [];

        $classNameList = array_merge(
            $className ? [$className] : [],
            $classNameList
        );

        return array_map(
            fn ($className) => $this->injectableFactory->createWith($className, ['entityType' => $entityType]),
            $classNameList
        );
    }
}
