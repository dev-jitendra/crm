<?php


namespace Espo\Modules\Crm\Classes\Select\CampaignLogRecord\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Bounced implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'action' => 'Bounced',
        ]);
    }
}
