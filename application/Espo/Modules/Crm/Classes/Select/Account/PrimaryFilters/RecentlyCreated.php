<?php


namespace Espo\Modules\Crm\Classes\Select\Account\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use DateTime;

class RecentlyCreated implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $from = (new DateTime())
            ->modify('-7 days')
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        $queryBuilder->where([
            'createdAt>=' => $from,
        ]);
    }
}
