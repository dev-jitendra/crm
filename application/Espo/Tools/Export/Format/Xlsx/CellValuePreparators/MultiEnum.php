<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Utils\Language;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;
use Espo\Tools\Export\Format\Xlsx\FieldHelper;

class MultiEnum implements CellValuePreparator
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

        $list = $entity->get($name);

        if (!is_array($list)) {
            return null;
        }

        

        $fieldData = $this->fieldHelper->getData($entity->getEntityType(), $name);

        if (!$fieldData) {
            return $this->joinList($list);
        }

        $entityType = $fieldData->getEntityType();
        $field = $fieldData->getField();

        $translation = $this->ormDefs
            ->getEntity($entityType)
            ->getField($field)
            ->getParam('translation');

        if (!$translation) {
            return $this->joinList(
                array_map(
                    function ($item) use ($field, $entityType) {
                        return $this->language->translateOption($item, $field, $entityType);
                    },
                    $list
                )
            );
        }

        $map = $this->language->get($translation);

        if (!is_array($map)) {
            return $this->joinList($list);
        }

        return $this->joinList(
            array_map(
                function ($item) use ($map) {
                    return $map[$item] ?? $item;
                },
                $list
            )
        );
    }

    
    private function joinList(array $list): string
    {
        return implode(', ', $list);
    }
}
