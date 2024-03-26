<?php


namespace Espo\Classes\Select\Email\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\Email;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyTeam implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $subQuery = QueryBuilder::create()
            ->select('id')
            ->from(Email::ENTITY_TYPE)
            ->leftJoin(Team::RELATIONSHIP_ENTITY_TEAM, 'entityTeam', [
                'entityTeam.entityId:' => 'id',
                'entityTeam.entityType' => Email::ENTITY_TYPE,
                'entityTeam.deleted' => false,
            ])
            ->leftJoin(Email::RELATIONSHIP_EMAIL_USER, 'emailUser', [
                'emailUser.emailId:' => 'id',
                'emailUser.deleted' => false,
                'emailUser.userId' => $this->user->getId(),
            ])
            ->where([
                'OR' => [
                    'entityTeam.teamId' => $this->user->getTeamIdList(),
                    'emailUser.userId' => $this->user->getId(),
                ]
            ])
            ->build();

        $queryBuilder->where(
            Cond::in(
                Cond::column('id'),
                $subQuery
            )
        );
    }
}
