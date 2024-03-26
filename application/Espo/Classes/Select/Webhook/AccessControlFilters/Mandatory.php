<?php


namespace Espo\Classes\Select\Webhook\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;

class Mandatory implements Filter
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        if (!$this->user->isApi()) {
            $queryBuilder->where([
                'id' => null,
            ]);

            return;
        }

        $queryBuilder->where([
            'userId' => $this->user->getId()
        ]);
    }
}
