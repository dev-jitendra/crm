<?php


namespace Espo\Modules\Crm\Classes\Select\Contact\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class PortalUsers implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->join('portalUser', 'portalUserFilter');
    }
}
