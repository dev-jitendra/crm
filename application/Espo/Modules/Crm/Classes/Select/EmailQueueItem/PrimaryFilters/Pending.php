<?php


namespace Espo\Modules\Crm\Classes\Select\EmailQueueItem\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\EmailQueueItem;
use Espo\ORM\Query\SelectBuilder;

class Pending implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'status' => EmailQueueItem::STATUS_PENDING,
        ]);
    }
}
