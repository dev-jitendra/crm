<?php


namespace Espo\Core\Select\Order\ItemConverters;

use Espo\ORM\Query\Part\OrderList;
use Espo\ORM\Query\Part\Order;

use Espo\Core\Select\Order\Item;
use Espo\Core\Select\Order\ItemConverter;

class AddressType implements ItemConverter
{
    public function convert(Item $item): OrderList
    {
        $orderBy = $item->getOrderBy();
        $order = $item->getOrder();

        return OrderList::create([
            Order::fromString($orderBy . 'Country')->withDirection($order),
            Order::fromString($orderBy . 'City')->withDirection($order),
            Order::fromString($orderBy . 'Street')->withDirection($order),
        ]);
    }
}
