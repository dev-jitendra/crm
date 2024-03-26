<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Core\Select\Helpers\FieldHelper;
use Espo\Entities\User;
use Espo\ORM\Defs;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyOwn implements Filter
{
    public function __construct(
        private User $user,
        private FieldHelper $fieldHelper,
        private string $entityType,
        private Defs $defs
    ) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        if ($this->fieldHelper->hasAssignedUsersField()) {
            $relationDefs = $this->defs
                ->getEntity($this->entityType)
                ->getRelation('assignedUsers');

            $middleEntityType = ucfirst($relationDefs->getRelationshipName());
            $key1 = $relationDefs->getMidKey();
            $key2 = $relationDefs->getForeignMidKey();

            $subQuery = QueryBuilder::create()
                ->select('id')
                ->from($this->entityType)
                ->leftJoin($middleEntityType, 'assignedUsersMiddle', [
                    "assignedUsersMiddle.{$key1}:" => 'id',
                    'assignedUsersMiddle.deleted' => false,
                ])
                ->where(["assignedUsersMiddle.{$key2}" => $this->user->getId()])
                ->build();

            $queryBuilder->where(['id=s' => $subQuery]);

            return;
        }

        if ($this->fieldHelper->hasAssignedUserField()) {
            $queryBuilder->where(['assignedUserId' => $this->user->getId()]);

            return;
        }

        if ($this->fieldHelper->hasCreatedByField()) {
            $queryBuilder->where(['createdById' => $this->user->getId()]);
        }
    }
}
