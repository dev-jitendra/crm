<?php


namespace Espo\Core\Record\Duplicator;

use Espo\Core\ORM\Type\FieldType;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\ORM\Defs;
use Espo\ORM\Defs\FieldDefs;
use Espo\Core\Utils\FieldUtil;

use stdClass;


class EntityDuplicator
{
    public function __construct(
        private Defs $defs,
        private FieldDuplicatorFactory $fieldDuplicatorFactory,
        private FieldUtil $fieldUtil,
        private Metadata $metadata
    ) {}

    public function duplicate(Entity $entity): stdClass
    {
        $entityType = $entity->getEntityType();
        $valueMap = $entity->getValueMap();

        unset($valueMap->id);

        $entityDefs = $this->defs->getEntity($entityType);

        foreach ($entityDefs->getFieldList() as $fieldDefs) {
            $this->processField($entity, $fieldDefs, $valueMap);
        }

        return $valueMap;
    }

    private function processField(Entity $entity, FieldDefs $fieldDefs, stdClass $valueMap): void
    {
        $entityType = $entity->getEntityType();
        $field = $fieldDefs->getName();

        if ($this->toIgnoreField($entityType, $fieldDefs)) {
            $attributeList = $this->fieldUtil->getAttributeList($entityType, $field);

            foreach ($attributeList as $attribute) {
                unset($valueMap->$attribute);
            }

            return;
        }

        if (!$this->fieldDuplicatorFactory->has($entityType, $field)) {
            return;
        }

        $fieldDuplicator = $this->fieldDuplicatorFactory->create($entityType, $field);

        $fieldValueMap = $fieldDuplicator->duplicate($entity, $field);

        foreach (get_object_vars($fieldValueMap) as $attribute => $value) {
            $valueMap->$attribute = $value;
        }
    }

    private function toIgnoreField(string $entityType, FieldDefs $fieldDefs): bool
    {
        $type = $fieldDefs->getType();

        if (in_array($type, [FieldType::AUTOINCREMENT, FieldType::NUMBER])) {
            return true;
        }

        if ($this->metadata->get(['scopes', $entityType, 'statusField']) === $fieldDefs->getName()) {
            return true;
        }

        return (bool) $fieldDefs->getParam('duplicateIgnore');
    }
}
