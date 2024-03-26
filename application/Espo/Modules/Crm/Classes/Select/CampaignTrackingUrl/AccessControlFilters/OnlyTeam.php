<?php


namespace Espo\Modules\Crm\Classes\Select\CampaignTrackingUrl\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder;

use Espo\Entities\User;

class OnlyTeam implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->leftJoin('campaign', 'campaignAccess');

        $teamIdList = $this->user->getLinkMultipleIdList('teams');

        if (count($teamIdList) === 0) {
            $queryBuilder->where([
                'campaignAccess.assignedUserId' => $this->user->getId(),
            ]);

            return;
        }

        $queryBuilder
            ->leftJoin(
                'EntityTeam',
                'entityTeamAccess',
                [
                    'entityTeamAccess.entityType' => 'Campaign',
                    'entityTeamAccess.entityId:' => 'campaignAccess.id',
                    'entityTeamAccess.deleted' => false,
                ]
            )
            ->where([
                'OR' => [
                    'entityTeamAccess.teamId' => $teamIdList,
                    'campaignAccess.assignedUserId' => $this->user->getId(),
                ],
                'campaignId!=' => null,
            ]);
    }
}
