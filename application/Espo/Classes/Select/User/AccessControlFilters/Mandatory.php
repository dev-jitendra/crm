<?php


namespace Espo\Classes\Select\User\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Acl\Table;
use Espo\Core\AclManager;
use Espo\Core\Select\AccessControl\Filter;
use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(
        private User $user,
        private AclManager $aclManager
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if (!$this->user->isAdmin()) {
            $queryBuilder->where([
                'isActive' => true,
                'type!=' => User::TYPE_API,
            ]);
        }

        if ($this->aclManager->getPermissionLevel($this->user, 'portalPermission') !== Table::LEVEL_YES) {
            $queryBuilder->where([
                'OR' => [
                    'type!=' => User::TYPE_PORTAL,
                    'id' => $this->user->getId(),
                ]
            ]);
        }

        if (!$this->user->isSuperAdmin()) {
            $queryBuilder->where([
                'type!=' => User::TYPE_SUPER_ADMIN,
            ]);
        }

        $queryBuilder->where([
            'type!=' => User::TYPE_SYSTEM,
        ]);
    }
}
