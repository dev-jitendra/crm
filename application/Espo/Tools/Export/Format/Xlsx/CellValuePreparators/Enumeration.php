<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Utils\Language;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;
use Espo\Tools\Export\Format\Xlsx\FieldHelper;

class Enumeration implements CellValuePreparator
{
    public function __construct(
        private Defs $ormDefs,
        private Language $language,
        private FieldHelper $fieldHelper
    ) {}

    public function prepare(Entity $entity, string $name): ?string
    {
        if (!$entity->has($name)) {
            return null;
        }

        $value = $entity->get($name);

        $fieldData = $this->fieldHelper->getData($entity->getEntityType(), $name);

        if (!$fieldData) {
            return $value;
        }

        $entityType = $fieldData->getEntityType();
        $field = $fieldData->getField();

        $translation = $this->ormDefs
            ->getEntity($entityType)
            ->getField($field)
            ->getParam('translation');

        if (!$translation) {
            return $this->language->translateOption($value, $field, $entityType);
        }

        $map = $this->language->get($translation);

        if (!is_array($map)) {
            return $value;
        }

        return $map[$value] ?? $value;
    }
}
