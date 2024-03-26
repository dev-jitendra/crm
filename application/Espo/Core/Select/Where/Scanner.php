<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Select\Where\Item\Type;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\ORM\BaseEntity;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\ORM\QueryComposer\BaseQueryComposer as QueryComposer;
use RuntimeException;


class Scanner
{
    
    private array $seedHash = [];

    
    private $nestingTypeList = [
        Type::OR,
        Type::AND,
    ];

    
    private array $subQueryTypeList = [
        Type::SUBQUERY_IN,
        Type::SUBQUERY_NOT_IN,
        Type::NOT,
    ];

    public function __construct(private EntityManager $entityManager)
    {}

    
    public function apply(QueryBuilder $queryBuilder, Item $item): void
    {
        $entityType = $queryBuilder->build()->getFrom();

        if (!$entityType) {
            throw new RuntimeException("No entity type.");
        }

        $this->applyLeftJoinsFromItem($queryBuilder, $item, $entityType);
    }

    private function applyLeftJoinsFromItem(QueryBuilder $queryBuilder, Item $item, string $entityType): void
    {
        $type = $item->getType();
        $value = $item->getValue();
        $attribute = $item->getAttribute();

        if (in_array($type, $this->subQueryTypeList)) {
            return;
        }

        if (in_array($type, $this->nestingTypeList)) {
            if (!is_array($value)) {
                return;
            }

            foreach ($value as $subItem) {
                $this->applyLeftJoinsFromItem($queryBuilder, Item::fromRaw($subItem), $entityType);
            }

            return;
        }

        if (!$attribute) {
            return;
        }

        $this->applyLeftJoinsFromAttribute($queryBuilder, $attribute, $entityType);
    }

    private function applyLeftJoinsFromAttribute(
        QueryBuilder $queryBuilder,
        string $attribute,
        string $entityType
    ): void {

        if (str_contains($attribute, ':')) {
            $argumentList = QueryComposer::getAllAttributesFromComplexExpression($attribute);

            foreach ($argumentList as $argument) {
                $this->applyLeftJoinsFromAttribute($queryBuilder, $argument, $entityType);
            }

            return;
        }

        $seed = $this->getSeed($entityType);

        if (str_contains($attribute, '.')) {
            list($link, $attribute) = explode('.', $attribute);

            if ($seed->hasRelation($link)) {
                $queryBuilder->leftJoin($link);

                if (
                    in_array($seed->getRelationType($link), [Entity::HAS_MANY, Entity::MANY_MANY])
                ) {
                    $queryBuilder->distinct();
                }
            }

            return;
        }

        $attributeType = $seed->getAttributeType($attribute);

        if ($attributeType === Entity::FOREIGN) {
            $relation = $this->getAttributeParam($seed, $attribute, 'relation');

            if ($relation) {
                $queryBuilder->leftJoin($relation);
            }
        }
    }

    private function getSeed(string $entityType): Entity
    {
        if (!isset($this->seedHash[$entityType])) {
            $this->seedHash[$entityType] = $this->entityManager->getNewEntity($entityType);
        }

        return $this->seedHash[$entityType];
    }

    
    private function getAttributeParam(Entity $entity, string $attribute, string $param)
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getAttributeParam($attribute, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasAttribute($attribute)) {
            return null;
        }

        return $entityDefs->getAttribute($attribute)->getParam($param);
    }
}
