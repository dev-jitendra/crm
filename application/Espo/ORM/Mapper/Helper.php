<?php


namespace Espo\ORM\Mapper;

use Espo\ORM\Entity;
use Espo\ORM\Metadata;

use RuntimeException;

class Helper
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function getRelationKeys(Entity $entity, string $relationName): array
    {
        $entityType = $entity->getEntityType();

        $defs = $this->metadata->getDefs()
            ->getEntity($entityType)
            ->getRelation($relationName);

        $type = $defs->getType();

        switch ($type) {

            case Entity::BELONGS_TO:
                $key = $defs->hasKey() ?
                    $defs->getKey() :
                    $relationName . 'Id';

                $foreignKey = $defs->hasForeignKey() ?
                    $defs->getForeignKey() :
                    'id';

                return [
                    'key' => $key,
                    'foreignKey' => $foreignKey,
                ];

            case Entity::HAS_MANY:
            case Entity::HAS_ONE:
                $key = $defs->hasKey() ? $defs->getKey() : 'id';

                $foreign = $defs->hasForeignRelationName() ?
                    $defs->getForeignRelationName() :
                    null;

                $foreignKey = $defs->hasForeignKey() ?
                    $defs->getForeignKey() :
                    null;

                if (!$foreignKey && $foreign) {
                    $foreignKey = $foreign . 'Id';
                }

                if (!$foreignKey) {
                    $foreignKey = lcfirst($entity->getEntityType()) . 'Id';
                }

                return [
                    'key' => $key,
                    'foreignKey' => $foreignKey,
                ];

            case Entity::HAS_CHILDREN:
                $key = $defs->hasKey() ? $defs->getKey() : 'id';

                $foreignKey = $defs->hasForeignKey() ?
                    $defs->getForeignKey() :
                    'parentId';

                $foreignType = $defs->getParam('foreignType') ?? 'parentType';

                return [
                    'key' => $key,
                    'foreignKey' => $foreignKey,
                    'foreignType' => $foreignType,
                ];

            case Entity::MANY_MANY:
                $key = $defs->hasKey() ?
                    $defs->getKey() :
                    'id';

                $foreignKey = $defs->hasForeignKey() ?
                    $defs->getForeignKey() :
                    'id';

                $nearKey = $defs->hasMidKey() ?
                    $defs->getMidKey() :
                    lcfirst($entityType) . 'Id';

                $distantKey = $defs->hasForeignMidKey() ?
                    $defs->getForeignMidKey() :
                    lcfirst($defs->getForeignEntityType()) . 'Id';

                return [
                    'key' => $key,
                    'foreignKey' => $foreignKey,
                    'nearKey' => $nearKey,
                    'distantKey' => $distantKey,
                ];

            case Entity::BELONGS_TO_PARENT:
                $key = $relationName . 'Id';
                $typeKey = $relationName . 'Type';

                return [
                    'key' => $key,
                    'typeKey' => $typeKey,
                    'foreignKey' => 'id',
                ];
        }

        throw new RuntimeException("Relation type '{$type}' not supported for 'getKeys'.");
    }
}
