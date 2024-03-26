<?php


namespace Espo\Core\Select\Select;

use Espo\Core\Select\SearchParams;
use Espo\Core\Utils\FieldUtil;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Applier
{
    
    private $aclAttributeList = [
        'assignedUserId',
        'createdById',
    ];

    
    private $aclPortalAttributeList = [
        'assignedUserId',
        'createdById',
        'contactId',
        'accountId',
    ];

    public function __construct(
        private string $entityType,
        private User $user,
        private FieldUtil $fieldUtil,
        private MetadataProvider $metadataProvider
    ) {}

    public function apply(QueryBuilder $queryBuilder, SearchParams $searchParams): void
    {
        $attributeList = $this->getSelectAttributeList($searchParams);

        if ($attributeList) {
            $queryBuilder->select(
                $this->prepareAttributeList($attributeList, $searchParams)
            );
        }
    }

    
    private function prepareAttributeList(array $attributeList, SearchParams $searchParams): array
    {
        $limit = $searchParams->getMaxTextAttributeLength();

        if ($limit === null) {
            return $attributeList;
        }

        $resultList = [];

        foreach ($attributeList as $item) {
            if (
                $this->metadataProvider->hasAttribute($this->entityType, $item) &&
                $this->metadataProvider->getAttributeType($this->entityType, $item) === Entity::TEXT &&
                !$this->metadataProvider->isAttributeNotStorable($this->entityType, $item)
            ) {
                $resultList[] = [
                    "LEFT:({$item}, {$limit})",
                    $item
                ];

                continue;
            }

            $resultList[] = $item;
        }

        return $resultList;
    }

    
    private function getSelectAttributeList(SearchParams $searchParams): ?array
    {
        $passedAttributeList = $searchParams->getSelect();

        if (!$passedAttributeList) {
            return null;
        }

        if ($passedAttributeList === ['*']) {
            return ['*'];
        }

        $attributeList = [];

        if (!in_array('id', $passedAttributeList)) {
            $attributeList[] = 'id';
        }

        foreach ($this->getAclAttributeList() as $attribute) {
            if (in_array($attribute, $passedAttributeList)) {
                continue;
            }

            if (!$this->metadataProvider->hasAttribute($this->entityType, $attribute)) {
                continue;
            }

            $attributeList[] = $attribute;
        }

        foreach ($passedAttributeList as $attribute) {
            if (in_array($attribute, $attributeList)) {
                continue;
            }

            if (!$this->metadataProvider->hasAttribute($this->entityType, $attribute)) {
                continue;
            }

            $attributeList[] = $attribute;
        }

        $orderByField = $searchParams->getOrderBy() ?? $this->metadataProvider->getDefaultOrderBy($this->entityType);

        if ($orderByField) {
            $sortByAttributeList = $this->fieldUtil->getAttributeList($this->entityType, $orderByField);

            foreach ($sortByAttributeList as $attribute) {
                if (in_array($attribute, $attributeList)) {
                    continue;
                }

                if (!$this->metadataProvider->hasAttribute($this->entityType, $attribute)) {
                    continue;
                }

                $attributeList[] = $attribute;
            }
        }

        $selectAttributesDependencyMap =
            $this->metadataProvider->getSelectAttributesDependencyMap($this->entityType) ?? [];

        foreach ($selectAttributesDependencyMap as $attribute => $dependantAttributeList) {
            if (!in_array($attribute, $attributeList)) {
                continue;
            }

            foreach ($dependantAttributeList as $dependantAttribute) {
                if (in_array($dependantAttribute, $attributeList)) {
                    continue;
                }

                $attributeList[] = $dependantAttribute;
            }
        }

        return $attributeList;
    }

    
    private function getAclAttributeList(): array
    {
        if ($this->user->isPortal()) {
            return
                $this->metadataProvider->getAclPortalAttributeList($this->entityType) ??
                $this->aclPortalAttributeList;
        }

        return
            $this->metadataProvider->getAclAttributeList($this->entityType) ??
            $this->aclAttributeList;
    }
}
