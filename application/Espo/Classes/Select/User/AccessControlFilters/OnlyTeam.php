<?php


namespace Espo\Classes\Select\User\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Acl\Table;
use Espo\Core\AclManager;
use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;

class OnlyTeam implements Filter
{
    public function __construct(
        private User $user,
        private AclManager $aclManager
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $orGroup = [
            'teamsAccess.id' => $this->user->getLinkMultipleIdList('teams'),
            'id' => $this->user->getId(),
        ];

        if ($this->aclManager->getPermissionLevel($this->user, 'portalPermission') === Table::LEVEL_YES) {
            $orGroup['type'] = User::TYPE_PORTAL;
        }

        $queryBuilder
            ->distinct()
            ->leftJoin('teams', 'teamsAccess')
            ->where([
               'OR' => $orGroup,
            ]);
    }
}
