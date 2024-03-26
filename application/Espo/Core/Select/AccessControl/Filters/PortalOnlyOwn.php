<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Core\Select\Helpers\FieldHelper;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class PortalOnlyOwn implements Filter
{
    public function __construct(private User $user, private FieldHelper $fieldHelper)
    {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        if ($this->fieldHelper->hasCreatedByField()) {
            $queryBuilder->where([
                'createdById' => $this->user->getId(),
            ]);

            return;
        }

        $queryBuilder->where(['id' => null]);
    }
}
