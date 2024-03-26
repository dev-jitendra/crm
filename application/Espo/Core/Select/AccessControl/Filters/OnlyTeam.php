<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Core\Select\Helpers\FieldHelper;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\ORM\Defs;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyTeam implements Filter
{
    public function __construct(
        private User $user,
        private FieldHelper $fieldHelper,
        private string $entityType,
        private Defs $defs
    ) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        if (!$this->fieldHelper->hasTeamsField()) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $subQueryBuilder = QueryBuilder::create()
            ->select('id')
            ->from($this->entityType)
            ->leftJoin(Team::RELATIONSHIP_ENTITY_TEAM, 'entityTeam', [
                'entityTeam.entityId:' => 'id',
                'entityTeam.entityType' => $this->entityType,
                'entityTeam.deleted' => false,
            ]);

        
        $orGroup = ['entityTeam.teamId' => $this->user->getTeamIdList()];

        if ($this->fieldHelper->hasAssignedUsersField()) {
            $relationDefs = $this->defs
                ->getEntity($this->entityType)
                ->getRelation('assignedUsers');

            $middleEntityType = ucfirst($relationDefs->getRelationshipName());
            $key1 = $relationDefs->getMidKey();
            $key2 = $relationDefs->getForeignMidKey();

            $subQueryBuilder->leftJoin($middleEntityType, 'assignedUsersMiddle', [
                "assignedUsersMiddle.{$key1}:" => 'id',
                'assignedUsersMiddle.deleted' => false,
            ]);

            $orGroup["assignedUsersMiddle.{$key2}"] = $this->user->getId();
        }
        else if ($this->fieldHelper->hasAssignedUserField()) {
            $orGroup['assignedUserId'] = $this->user->getId();
        }
        else if ($this->fieldHelper->hasCreatedByField()) {
            $orGroup['createdById'] = $this->user->getId();
        }

        $subQuery = $subQueryBuilder
            ->where(['OR' => $orGroup])
            ->build();

        $queryBuilder->where(['id=s' => $subQuery]);
    }
}
