<?php


namespace Espo\Modules\Crm\Classes\Select\Account\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\Account;
use Espo\ORM\Query\SelectBuilder;

class Customers implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where(['type' => Account::TYPE_CUSTOMER]);
    }
}
