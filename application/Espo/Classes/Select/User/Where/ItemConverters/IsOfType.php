<?php


namespace Espo\Classes\Select\User\Where\ItemConverters;

use Espo\Core\Select\Where\Item;
use Espo\Core\Select\Where\ItemConverter;
use Espo\Entities\User;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\WhereItem as WhereClauseItem;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class IsOfType implements ItemConverter
{
    public function convert(QueryBuilder $queryBuilder, Item $item): WhereClauseItem
    {
        $type = $item->getValue();

        return match ($type) {
            'internal' => WhereClause::fromRaw([
                'type!=' => [
                    User::TYPE_PORTAL,
                    User::TYPE_API,
                    User::TYPE_SYSTEM,
                ],
            ]),
            User::TYPE_PORTAL => WhereClause::fromRaw([
                'type' => User::TYPE_PORTAL,
            ]),
            User::TYPE_API => WhereClause::fromRaw([
                'type' => User::TYPE_API,
            ]),
            default => WhereClause::fromRaw(['id' => null]),
        };
    }
}
