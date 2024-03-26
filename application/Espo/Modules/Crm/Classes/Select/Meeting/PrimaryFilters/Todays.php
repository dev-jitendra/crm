<?php


namespace Espo\Modules\Crm\Classes\Select\Meeting\PrimaryFilters;

use Espo\Core\Exceptions\Error;
use Espo\Entities\User;
use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Select\Helpers\UserTimeZoneProvider;
use Espo\Core\Select\Where\ConverterFactory;
use Espo\Core\Select\Where\Item;
use Espo\Modules\Crm\Entities\Meeting;
use LogicException;

class Todays implements Filter
{
    public function __construct(
        private User $user,
        private UserTimeZoneProvider $userTimeZoneProvider,
        private ConverterFactory $converterFactory
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $item = Item::fromRaw([
            'type' => Item\Type::TODAY,
            'attribute' => 'dateStart',
            'timeZone' => $this->userTimeZoneProvider->get(),
            'dateTime' => true,
        ]);

        try {
            $whereItem = $this->converterFactory
                ->create(Meeting::ENTITY_TYPE, $this->user)
                ->convert($queryBuilder, $item);
        }
        catch (Error $e) {
            throw new LogicException($e->getMessage());
        }

        $queryBuilder->where($whereItem);
    }
}
