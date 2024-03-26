<?php


namespace Espo\Classes\Select\User\OrderItemConverters;

use Espo\Core\Select\Order\ItemConverter;
use Espo\Core\Select\Order\Item;

use Espo\ORM\Query\Part\OrderList;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Expression as Expr;

use Espo\Entities\User;

class UserNameOwnFirst implements ItemConverter
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function convert(Item $item): OrderList
    {
        return OrderList::create([
            Order
                ::create(
                    Expr::notEqual(
                        Expr::column('id'),
                        $this->user->getId()
                    )
                )
                ->withDirection($item->getOrder()),
            Order
                ::create(Expr::column('userName'))
                ->withDirection($item->getOrder()),
        ]);
    }
}
