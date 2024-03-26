<?php


namespace Espo\Classes\Select\Email\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Classes\Select\Email\Helpers\JoinHelper;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class PortalOnlyContact implements Filter
{
    public function __construct(private User $user, private JoinHelper $joinHelper)
    {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $this->joinHelper->joinEmailUser($queryBuilder, $this->user->getId());

        $queryBuilder->distinct();

        $orGroup = [
            'emailUser.userId' => $this->user->getId(),
        ];

        $contactId = $this->user->get('contactId');

        if ($contactId) {
            $orGroup[] = [
                'parentId' => $contactId,
                'parentType' => 'Contact',
            ];
        }

        $queryBuilder->where([
            'OR' => $orGroup,
        ]);
    }
}
