<?php


namespace Espo\Classes\Select\ImportError\OrderItemConverters;

use Espo\Core\Select\Order\ItemConverter;
use Espo\Core\Select\Order\Item;

use Espo\ORM\Query\Part\OrderList;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Expression as Expr;

class ExportLineNumber implements ItemConverter
{
    public function convert(Item $item): OrderList
    {
        return OrderList::create([
            Order
                ::create(Expr::column('exportRowIndex'))
                ->withDirection($item->getOrder())
        ]);
    }
}
