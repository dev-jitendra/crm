<?php


namespace Espo\Core\Select\Order\ItemConverters;

use Espo\ORM\Query\Part\OrderList;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Expression;
use Espo\Core\Select\Order\Item;
use Espo\Core\Select\Order\ItemConverter;
use Espo\Core\Select\SearchParams;
use Espo\Core\Utils\Metadata;

class EnumType implements ItemConverter
{
    public function __construct(
        private string $entityType,
        private Metadata $metadata
    ) {}

    public function convert(Item $item): OrderList
    {
        $orderBy = $item->getOrderBy();
        $order = $item->getOrder();

        
        $list = $this->metadata->get([
            'entityDefs', $this->entityType, 'fields', $orderBy, 'options'
        ]);

        if (!is_array($list) || !count($list)) {
            return OrderList::create([
                Order::fromString($orderBy)->withDirection($order)
            ]);
        }

        $isSorted = $this->metadata->get([
            'entityDefs', $this->entityType, 'fields', $orderBy, 'isSorted'
        ]);

        if ($isSorted) {
            asort($list);
        }

        if ($order === SearchParams::ORDER_DESC) {
            $list = array_reverse($list);
        }

        return OrderList::create([
            Order::createByPositionInList(Expression::column($orderBy), $list),
        ]);
    }
}
