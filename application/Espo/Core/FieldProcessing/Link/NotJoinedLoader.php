<?php


namespace Espo\Core\FieldProcessing\Link;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;
use Espo\ORM\Defs as OrmDefs;


class NotJoinedLoader implements LoaderInterface
{
    private OrmDefs $ormDefs;

    private EntityManager $entityManager;

    
    private $fieldListCacheMap = [];

    public function __construct(OrmDefs $ormDefs, EntityManager $entityManager)
    {
        $this->ormDefs = $ormDefs;
        $this->entityManager = $entityManager;
    }

    public function process(Entity $entity, Params $params): void
    {
        foreach ($this->getFieldList($entity->getEntityType()) as $field) {
            $this->processItem($entity, $field);
        }
    }

    private function processItem(Entity $entity, string $field): void
    {
        $nameAttribute = $field . 'Name';
        $idAttribute = $field . 'Id';

        $id = $entity->get($idAttribute);

        if (!$id) {
            $entity->set($nameAttribute, null);

            return;
        }

        if ($entity->get($nameAttribute)) {
            return;
        }

        $foreignEntityType = $this->ormDefs
            ->getEntity($entity->getEntityType())
            ->getRelation($field)
            ->getForeignEntityType();

        $foreignEntity = $this->entityManager
            ->getRDBRepository($foreignEntityType)
            ->select(['id', 'name'])
            ->where(['id' => $id])
            ->findOne();

        if (!$foreignEntity) {
            $entity->set($nameAttribute, null);

            return;
        }

        $entity->set($nameAttribute, $foreignEntity->get('name'));
    }

    
    private function getFieldList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->fieldListCacheMap)) {
            return $this->fieldListCacheMap[$entityType];
        }

        $list = [];

        $entityDefs = $this->ormDefs->getEntity($entityType);

        foreach ($entityDefs->getRelationList() as $relationDefs) {
            if ($relationDefs->getType() !== Entity::BELONGS_TO) {
                continue;
            }

            if (!$relationDefs->getParam('noJoin')) {
                continue;
            }

            if (!$relationDefs->hasForeignEntityType()) {
                continue;
            }

            $foreignEntityType = $relationDefs->getForeignEntityType();

            if (!$this->entityManager->hasRepository($foreignEntityType)) {
                continue;
            }

            $name = $relationDefs->getName();

            if (!$entityDefs->hasAttribute($name . 'Id')) {
                continue;
            }

            if (!$entityDefs->hasAttribute($name . 'Name')) {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListCacheMap[$entityType] = $list;

        return $list;
    }
}
