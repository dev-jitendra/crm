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

class ActualStartingNotInFuture implements Filter
{
    public function __construct(
        private User $user,
        private UserTimeZoneProvider $userTimeZoneProvider,
        private Metadata $metadata,
        private ConverterFactory $converterFactory
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $notActualStatusList = $this->metadata
            ->get(['entityDefs', 'Task', 'fields', 'status', 'notActualOptions']) ?? [];

        $converter = $this->converterFactory->create(Task::ENTITY_TYPE, $this->user);

        $queryBuilder->where(
            Cond::and(
                Cond::notIn(
                    Cond::column('status'),
                    $notActualStatusList
                ),
                Cond::or(
                    Cond::equal(
                        Cond::column('dateStart'),
                        null
                    ),
                    Cond::and(
                        Cond::notEqual(
                            Cond::column('dateStart'),
                            null
                        ),
                        Cond::or(
                            $converter->convert(
                                $queryBuilder,
                                Item::fromRaw([
                                    'type' => Item\Type::PAST,
                                    'attribute' => 'dateStart',
                                    'timeZone' => $this->userTimeZoneProvider->get(),
                                    'dateTime' => true,
                                ])
                            ),
                            $converter->convert(
                                $queryBuilder,
                                Item::fromRaw([
                                    'type' => Item\Type::TODAY,
                                    'attribute' => 'dateStart',
                                    'timeZone' => $this->userTimeZoneProvider->get(),
                                    'dateTime' => true,
                                ])
                            )
                        )
                    )
                )
            )
        );
    }
}
