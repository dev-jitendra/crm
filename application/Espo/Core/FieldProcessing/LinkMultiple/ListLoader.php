<?php


namespace Espo\Core\FieldProcessing\LinkMultiple;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;

use Espo\ORM\Defs as OrmDefs;


class ListLoader implements LoaderInterface
{
    
    private array $fieldListCacheMap = [];

    public function __construct(private OrmDefs $ormDefs)
    {}

    public function process(Entity $entity, Params $params): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        $entityType = $entity->getEntityType();

        $select = $params->getSelect() ?? [];

        if (count($select) === 0) {
            return;
        }

        foreach ($this->getFieldList($entityType) as $field) {
            if (
                !in_array($field . 'Ids', $select) &&
                !in_array($field . 'Names', $select)
            ) {
                continue;
            }

            $columns = $this->ormDefs
                ->getEntity($entityType)
                ->getField($field)
                ->getParam('columns');

            $entity->loadLinkMultipleField($field, $columns);
        }
    }

    
    private function getFieldList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->fieldListCacheMap)) {
            return $this->fieldListCacheMap[$entityType];
        }

        $list = [];

        $entityDefs = $this->ormDefs->getEntity($entityType);

        foreach ($entityDefs->getFieldList() as $fieldDefs) {
            if (
                $fieldDefs->getType() !== 'linkMultiple' &&
                $fieldDefs->getType() !== 'attachmentMultiple'
            ) {
                continue;
            }

            if ($fieldDefs->getParam('noLoad')) {
                continue;
            }

            if ($fieldDefs->isNotStorable()) {
                continue;
            }

            $name = $fieldDefs->getName();

            if (!$entityDefs->hasRelation($name)) {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListCacheMap[$entityType] = $list;

        return $list;
    }
}
