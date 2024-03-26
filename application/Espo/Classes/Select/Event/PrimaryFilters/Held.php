<?php


namespace Espo\Classes\Select\Event\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Query\SelectBuilder;

class Held implements Filter
{
    public function __construct(
        private string $entityType,
        private Metadata $metadata
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $statusList = $this->metadata->get(['scopes', $this->entityType, 'completedStatusList']) ?? [];

        $queryBuilder->where(['status' => $statusList]);
    }
}
