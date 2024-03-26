<?php


namespace Espo\Core\Select\Order;

use Espo\ORM\Query\Part\OrderList;


interface ItemConverter
{
    public function convert(Item $item): OrderList;
}
