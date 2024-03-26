<?php


namespace Espo\Core\FieldProcessing\LinkParent;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;

use Espo\ORM\Defs as OrmDefs;


class Loader implements LoaderInterface
{
    
    private array $fieldListCacheMap = [];

    public function __construct(private OrmDefs $ormDefs)
    {}

    public function process(Entity $entity, Params $params): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($this->getFieldList($entity->getEntityType()) as $field) {
            $entity->loadParentNameField($field);
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
            if ($fieldDefs->getType() !== 'linkParent') {
                continue;
            }

            $name = $fieldDefs->getName();

            if (!$entityDefs->hasRelation($fieldDefs->getName())) {
                
                
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListCacheMap[$entityType] = $list;

        return $list;
    }
}
