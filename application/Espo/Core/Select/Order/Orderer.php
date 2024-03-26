<?php


namespace Espo\Core\Select\Order;

use Espo\ORM\Query\SelectBuilder;


interface Orderer
{
    public function apply(SelectBuilder $queryBuilder, Item $item): void;
}
