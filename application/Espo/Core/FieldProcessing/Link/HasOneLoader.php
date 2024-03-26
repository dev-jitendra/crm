<?php


namespace Espo\Core\FieldProcessing\Link;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;

use Espo\ORM\Defs as OrmDefs;


class HasOneLoader implements LoaderInterface
{
    private OrmDefs $ormDefs;

    
    private $fieldListCacheMap = [];

    public function __construct(OrmDefs $ormDefs)
    {
        $this->ormDefs = $ormDefs;
    }

    public function process(Entity $entity, Params $params): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($this->getFieldList($entity->getEntityType()) as $field) {
            if ($entity->get($field . 'Name')) {
                continue;
            }

            $entity->loadLinkField($field);
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
            if ($fieldDefs->getType() !== 'link') {
                continue;
            }

            if ($fieldDefs->getParam('noLoad')) {
                continue;
            }

            $name = $fieldDefs->getName();

            if (!$entityDefs->hasRelation($name)) {
                continue;
            }

            if ($entityDefs->getRelation($name)->getType() !== Entity::HAS_ONE) {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListCacheMap[$entityType] = $list;

        return $list;
    }
}
