<?php


namespace Espo\Modules\Crm\Classes\Select\Task\PrimaryFilters;

use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\Core\Select\Primary\Filter;
use Espo\Core\Select\Helpers\UserTimeZoneProvider;
use Espo\Core\Select\Where\Item;
use Espo\Core\Select\Where\ConverterFactory;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Task;

class Overdue implements Filter
{
    public function __construct(
        private User $user,
        private UserTimeZoneProvider $userTimeZoneProvider,
        private Metadata $metadata,
        private ConverterFactory $converterFactory
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $notActualStatusList = array_filter(
            $this->metadata->get(['entityDefs', 'Task', 'fields', 'status', 'notActualOptions']) ?? [],
            fn(string $item) => $item !== Task::STATUS_DEFERRED
        );

        $pastItem = Item::fromRaw([
            'type' => Item\Type::PAST,
            'attribute' => 'dateEnd',
            'timeZone' => $this->userTimeZoneProvider->get(),
            'dateTime' => true,
        ]);

        $pastWhereItem = $this->converterFactory
            ->create(Task::ENTITY_TYPE, $this->user)
            ->convert($queryBuilder, $pastItem);

        $queryBuilder
            ->where($pastWhereItem)
            ->where(
                Cond::notIn(Cond::column('status'), $notActualStatusList)
            );
    }
}
