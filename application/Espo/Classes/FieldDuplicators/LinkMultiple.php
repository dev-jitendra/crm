<?php


namespace Espo\Classes\FieldDuplicators;

use Espo\Core\Record\Duplicator\FieldDuplicator;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use stdClass;

class LinkMultiple implements FieldDuplicator
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function duplicate(Entity $entity, string $field): stdClass
    {
        $valueMap = (object) [];

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entity->hasRelation($field)) {
            return $valueMap;
        }

        $relationDefs = $entityDefs->getRelation($field);

        if (
            !$relationDefs->hasForeignEntityType() ||
            !$relationDefs->hasForeignRelationName()
        ) {
            return $valueMap;
        }

        $foreignRelationType = $this->entityManager
            ->getDefs()
            ->getEntity($relationDefs->getForeignEntityType())
            ->getRelation($relationDefs->getForeignRelationName())
            ->getType();

        if ($foreignRelationType !== Entity::MANY_MANY) {
            $valueMap->{$field . 'Ids'} = [];
            $valueMap->{$field . 'Names'} = (object) [];
            $valueMap->{$field . 'Columns'} = (object) [];
        }

        return $valueMap;
    }
}
