<?php


namespace Espo\Classes\Select\Email\Where\ItemConverters;

use Espo\Core\Select\Where\Item;
use Espo\Core\Select\Where\ItemConverter;
use Espo\Classes\Select\Email\Helpers\JoinHelper;
use Espo\Entities\User;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\WhereItem as WhereClauseItem;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class IsImportantIsTrue implements ItemConverter
{
    public function __construct(private User $user, private JoinHelper $joinHelper)
    {}

    public function convert(QueryBuilder $queryBuilder, Item $item): WhereClauseItem
    {
        $this->joinHelper->joinEmailUser($queryBuilder, $this->user->getId());

        return WhereClause::fromRaw([
            'emailUser.isImportant' => true,
        ]);
    }
}
