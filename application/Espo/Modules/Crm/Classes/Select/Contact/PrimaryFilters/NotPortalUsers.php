<?php


namespace Espo\Modules\Crm\Classes\Select\Contact\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class NotPortalUsers implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('portalUser', 'portalUserFilter')
            ->where(['portalUserFilter.id' => null]);
    }
}
