<?php


namespace Espo\Modules\Crm\Classes\Select\Task\PrimaryFilters;

use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Task;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Select\Primary\Filter;

class Completed implements Filter
{
    public function __construct(
        private Metadata $metadata
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $statusList = $this->metadata->get(['scopes', Task::ENTITY_TYPE, 'completedStatusList']) ?? [];

        $queryBuilder->where(['status' => $statusList]);
    }
}
