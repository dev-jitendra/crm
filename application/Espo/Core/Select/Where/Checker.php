<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Acl;
use Espo\Core\Select\Where\Item\Type;
use Espo\ORM\QueryComposer\BaseQueryComposer as QueryComposer;
use Espo\ORM\QueryComposer\Util as QueryUtil;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\ORM\BaseEntity;


class Checker
{
    private ?Entity $seed = null;

    private const TYPE_IN_CATEGORY = 'inCategory';
    private const TYPE_IS_USER_FROM_TEAMS = 'isUserFromTeams';

    
    private $nestingTypeList = [
        Type::OR,
        Type::AND,
        Type::NOT,
        Type::SUBQUERY_IN,
        Type::SUBQUERY_NOT_IN,
    ];

    
    private $subQueryTypeList = [
        Type::SUBQUERY_IN,
        Type::SUBQUERY_NOT_IN,
        Type::NOT,
    ];

    
    private $linkTypeList = [
        self::TYPE_IN_CATEGORY,
        self::TYPE_IS_USER_FROM_TEAMS,
        Type::IS_LINKED_WITH_ANY,
        Type::IS_LINKED_WITH_NONE,
        Type::IS_LINKED_WITH,
        Type::IS_NOT_LINKED_WITH,
        Type::IS_LINKED_WITH_ALL,
    ];

    public function __construct(
        private string $entityType,
        private EntityManager $entityManager,
        private Acl $acl
    ) {}

    
    public function check(Item $item, Params $params): void
    {
        $this->checkItem($item, $params);
    }

    
    private function checkItem(Item $item, Params $params): void
    {
        $type = $item->getType();
        $attribute = $item->getAttribute();
        $value = $item->getValue();

        $forbidComplexExpressions = $params->forbidComplexExpressions();
        $checkWherePermission = $params->applyPermissionCheck();

        if ($forbidComplexExpressions) {
            if (in_array($type, $this->subQueryTypeList)) {
                throw new Forbidden("Sub-queries are forbidden in where.");
            }
        }

        if ($attribute && $forbidComplexExpressions) {
            if (QueryUtil::isComplexExpression($attribute)) {
                throw new Forbidden("Complex expressions are forbidden in where.");
            }
        }

        if ($attribute) {
            $argumentList = QueryComposer::getAllAttributesFromComplexExpression($attribute);

            foreach ($argumentList as $argument) {
                $this->checkAttributeExistence($argument, $type);

                if ($checkWherePermission) {
                    $this->checkAttributePermission($argument, $type);
                }
            }
        }

        if (in_array($type, $this->nestingTypeList) && is_array($value)) {
            foreach ($value as $subItem) {
                $this->checkItem(Item::fromRaw($subItem), $params);
            }
        }
    }

    
    private function checkAttributeExistence(string $attribute, string $type): void
    {
        if (str_contains($attribute, '.')) {
            
            return;
        }

        if (in_array($type, $this->linkTypeList)) {
            if (!$this->getSeed()->hasRelation($attribute)) {
                throw new BadRequest("Not existing relation '{$attribute}' in where.");
            }

            return;
        }

        if (!$this->getSeed()->hasAttribute($attribute)) {
            throw new BadRequest("Not existing attribute '{$attribute}' in where.");
        }
    }

    
    private function checkAttributePermission(string $attribute, string $type): void
    {
        $entityType = $this->entityType;

        if (str_contains($attribute, '.')) {
            list($link, $attribute) = explode('.', $attribute);

            if (!$this->getSeed()->hasRelation($link)) {
                
                throw new Forbidden("Bad relation '{$link}' in where.");
            }

            $foreignEntityType = $this->getRelationParam($this->getSeed(), $link, 'entity');

            if (!$foreignEntityType) {
                throw new Forbidden("Bad relation '{$link}' in where.");
            }

            if (
                !$this->acl->checkScope($foreignEntityType) ||
                in_array($link, $this->acl->getScopeForbiddenLinkList($entityType))
            ) {
                throw new Forbidden("Forbidden relation '{$link}' in where.");
            }

            if (in_array($attribute, $this->acl->getScopeForbiddenAttributeList($foreignEntityType))) {
                throw new Forbidden("Forbidden attribute '{$link}.{$attribute}' in where.");
            }

            return;
        }

        if (in_array($type, $this->linkTypeList)) {
            $link = $attribute;

            if (!$this->getSeed()->hasRelation($link)) {
                throw new Forbidden("Bad relation '{$link}' in where.");
            }

            $foreignEntityType = $this->getRelationParam($this->getSeed(), $link, 'entity');

            if (!$foreignEntityType) {
                throw new Forbidden("Bad relation '{$link}' in where.");
            }

            if (
                in_array($link, $this->acl->getScopeForbiddenFieldList($entityType)) ||
                !$this->acl->checkScope($foreignEntityType) ||
                in_array($link, $this->acl->getScopeForbiddenLinkList($entityType))
            ) {
                throw new Forbidden("Forbidden relation '{$link}' in where.");
            }

            return;
        }

        if (in_array($attribute, $this->acl->getScopeForbiddenAttributeList($entityType))) {
            throw new Forbidden("Forbidden attribute '{$attribute}' in where.");
        }
    }

    private function getSeed(): Entity
    {
        return $this->seed ?? $this->entityManager->getNewEntity($this->entityType);
    }

    private function getRelationParam(Entity $entity, string $relation, string $param): mixed
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getRelationParam($relation, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasRelation($relation)) {
            return null;
        }

        return $entityDefs->getRelation($relation)->getParam($param);
    }
}
