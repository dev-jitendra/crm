<?php


namespace Espo\Classes\Select\User\BoolFilters;

use Espo\Entities\User;

use Espo\Core\Select\Bool\Filter;

use Espo\ORM\Query\{
    SelectBuilder,
    Part\Where\OrGroupBuilder,
    Part\WhereClause,
};

class OnlyMyTeam implements Filter
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply(SelectBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        
        $teamIdList = $this->user->getLinkMultipleIdList('teams');

        if (count($teamIdList) === 0) {
            $orGroupBuilder->add(
                WhereClause::fromRaw([
                    'id' => null,
                ])
            );

            return;
        }

        $queryBuilder
            ->leftJoin('teams', 'teamsOnlyMyFilter')
            ->distinct();

        $orGroupBuilder->add(
            WhereClause::fromRaw([
                'teamsOnlyMyFilter.id' => $teamIdList
            ])
        );
    }
}
