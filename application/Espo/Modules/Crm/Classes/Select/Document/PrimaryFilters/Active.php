<?php


namespace Espo\Modules\Crm\Classes\Select\Document\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\Document;
use Espo\ORM\Query\SelectBuilder;

class Active implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder
            ->where([
                'status' => Document::STATUS_ACTIVE,
            ]);
    }
}
