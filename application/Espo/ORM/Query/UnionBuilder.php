<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\Order;

use InvalidArgumentException;

class UnionBuilder implements Builder
{
    use BaseBuilderTrait;

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): Union
    {
        return Union::fromRaw($this->params);
    }

    
    public function clone(Union $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function all(): self
    {
        $this->params['all'] = true;

        return $this;
    }

    public function query(Select $query): self
    {
        $this->params['queries'] = $this->params['queries'] ?? [];
        $this->params['queries'][] = $query;

        return $this;
    }

    
    public function limit(?int $offset = null, ?int $limit = null): self
    {
        $this->params['offset'] = $offset;
        $this->params['limit'] = $limit;

        return $this;
    }

    
    public function order($orderBy, string|bool $direction = Order::ASC): self
    {
        if (is_bool($direction)) {
            $direction = $direction ? Order::DESC : Order::ASC;
        }

        if (!$orderBy) {
            throw new InvalidArgumentException();
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $item) {
                

                if (count($item) === 2) {
                    
                    $this->order($item[0], $item[1]);

                    continue;
                }

                if (count($item) === 1) {
                    
                    $this->order($item[0]);

                    continue;
                }

                throw new InvalidArgumentException("Bad order.");
            }

            return $this;
        }

        

        if (!is_string($orderBy) && !is_int($orderBy)) {
            throw new InvalidArgumentException("Bad order.");
        }

        $this->params['orderBy'] = $this->params['orderBy'] ?? [];
        $this->params['orderBy'][] = [$orderBy, $direction];

        return $this;
    }
}
