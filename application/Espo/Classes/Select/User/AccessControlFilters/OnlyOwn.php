<?php


namespace Espo\Classes\Select\User\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Acl\Table;
use Espo\Core\AclManager;
use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;

class OnlyOwn implements Filter
{
    public function __construct(private User $user, private AclManager $aclManager)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->aclManager->getPermissionLevel($this->user, 'portalPermission') === Table::LEVEL_YES) {
            $queryBuilder->where([
                'OR' => [
                    'id' => $this->user->getId(),
                    'type' => User::TYPE_PORTAL,
                ],
            ]);

            return;
        }

        $queryBuilder->where([
            'id' => $this->user->getId(),
        ]);
    }
}
