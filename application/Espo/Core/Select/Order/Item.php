<?php


namespace Espo\Core\Select\Order;

use Espo\Core\Select\SearchParams;

use InvalidArgumentException;


class Item
{
    private string $orderBy;
    
    private string $order;

    
    private function __construct(string $orderBy, string $order)
    {
        if (
            $order !== SearchParams::ORDER_ASC &&
            $order !== SearchParams::ORDER_DESC
        ) {
            throw new InvalidArgumentException("Bad order.");
        }

        $this->orderBy = $orderBy;
        $this->order = $order;
    }

    
    public static function create(string $orderBy, ?string $order = null): self
    {
        if ($order === null) {
            $order = SearchParams::ORDER_ASC;
        }

        return new self($orderBy, $order);
    }

    
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    
    public function getOrder(): string
    {
        return $this->order;
    }
}
