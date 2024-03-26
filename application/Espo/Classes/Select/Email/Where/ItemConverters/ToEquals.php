<?php


namespace Espo\Classes\Select\Email\Where\ItemConverters;

use Espo\Core\Select\Helpers\RandomStringGenerator;
use Espo\Core\Select\Where\Item;
use Espo\Core\Select\Where\ItemConverter;
use Espo\Classes\Select\Email\Helpers\EmailAddressHelper;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\WhereItem as WhereClauseItem;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class ToEquals implements ItemConverter
{
    public function __construct(
        private EmailAddressHelper $emailAddressHelper,
        private RandomStringGenerator $randomStringGenerator
    ) {}

    public function convert(QueryBuilder $queryBuilder, Item $item): WhereClauseItem
    {
        $value = $item->getValue();

        if (!$value) {
            return WhereClause::fromRaw([
                'id' => null,
            ]);
        }

        $emailAddressId = $this->emailAddressHelper->getEmailAddressIdByValue($value);

        if (!$emailAddressId) {
            return WhereClause::fromRaw([
                'id' => null,
            ]);
        }

        $queryBuilder->distinct();

        $alias = 'emailEmailAddress' . $this->randomStringGenerator->generate();

        $queryBuilder->leftJoin(
            'EmailEmailAddress',
            $alias,
            [
                'emailId:' => 'id',
                'deleted' => false,
            ]
        );

        return WhereClause::fromRaw([
            $alias . '.emailAddressId' => $emailAddressId,
            $alias . '.addressType' => 'to',
        ]);
    }
}
