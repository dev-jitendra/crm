<?php


namespace Espo\Core\Select\Order;

use Espo\Core\Select\SearchParams;

use InvalidArgumentException;


class Params
{
    
    private bool $forceDefault = false;
    
    private $orderBy = null;
    
    private $order = null;

    private function __construct() {}

    
    public static function fromAssoc(array $params): self
    {
        $object = new self();

        
        $object->forceDefault = $params['forceDefault'] ?? false;
        $object->orderBy = $params['orderBy'] ?? null;
        $object->order = $params['order'] ?? null;

        foreach ($params as $key => $value) {
            if (!property_exists($object, $key)) {
                throw new InvalidArgumentException("Unknown parameter '{$key}'.");
            }
        }

        if ($object->orderBy && !is_string($object->orderBy)) {
            throw new InvalidArgumentException("Bad orderBy.");
        }

        
        $order = $object->order;

        if (
            $order &&
            $order !== SearchParams::ORDER_ASC &&
            $order !== SearchParams::ORDER_DESC
        ) {
            throw new InvalidArgumentException("Bad order.");
        }

        return $object;
    }

    

    
    public function forceDefault(): bool
    {
        return $this->forceDefault;
    }

    
    public function getOrderBy(): ?string
    {
        
        return $this->orderBy;
    }

    
    public function getOrder(): ?string
    {
        return $this->order;
    }
}
