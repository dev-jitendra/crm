<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Join;

use InvalidArgumentException;
use LogicException;
use RuntimeException;

trait SelectingBuilderTrait
{
    use BaseBuilderTrait;

    
    public function where($clause, $value = null): self
    {
        $this->applyWhereClause('whereClause', $clause, $value);

        return $this;
    }

    
    private function applyWhereClause(string $type, $clause, $value): void
    {
        if ($clause instanceof WhereItem) {
            $clause = $clause->getRaw();
        }

        $this->params[$type] = $this->params[$type] ?? [];

        $original = $this->params[$type];

        if (!is_string($clause) && !is_array($clause)) {
            throw new InvalidArgumentException("Bad where clause.");
        }

        if (is_array($clause)) {
            $new = $clause;
        }

        if (is_string($clause)) {
            $new = [$clause => $value];
        }

        $containsSameKeys = (bool) count(
            array_intersect(
                array_keys($new),
                array_keys($original)
            )
        );

        if ($containsSameKeys) {
            $this->params[$type][] = $new;

            return;
        }

        $this->params[$type] = $new + $original;
    }

    
    public function order($orderBy, $direction = null): self
    {
        if (is_bool($direction)) {
            $direction = $direction ? Order::DESC : Order::ASC;
        }

        if (is_array($orderBy)) {
            $this->params['orderBy'] = $this->normalizeOrderExpressionItemArray(
                $orderBy,
                $direction ?? Order::ASC
            );

            return $this;
        }

        if (!$orderBy) {
            throw new InvalidArgumentException();
        }

        $this->params['orderBy'] = $this->params['orderBy'] ?? [];

        if ($orderBy instanceof Expression) {
            $orderBy = $orderBy->getValue();
            $direction = $direction ?? Order::ASC;
        }
        else if ($orderBy instanceof Order) {
            $direction = $direction ?? $orderBy->getDirection();
            $orderBy = $orderBy->getExpression()->getValue();
        }
        else {
            $direction = $direction ?? Order::ASC;
        }

        $this->params['orderBy'][] = [$orderBy, $direction];

        return $this;
    }

    
    public function join(
        $target,
        ?string $alias = null,
        WhereItem|array|null $conditions = null
    ): self {

        return $this->joinInternal('joins', $target, $alias, $conditions);
    }

    
    public function leftJoin(
        $target,
        ?string $alias = null,
        WhereItem|array|null $conditions = null
    ): self {

        return $this->joinInternal('leftJoins', $target, $alias, $conditions);
    }

    
    private function joinInternal(
        string $type,
        $target,
        ?string $alias = null,
        WhereItem|array|null $conditions = null
    ): self {

        $onlyMiddle = false;

        

        if ($target instanceof Join) {
            $alias = $alias ?? $target->getAlias();
            $conditions = $conditions ?? $target->getConditions();
            $onlyMiddle = $target->isOnlyMiddle();
            $target = $target->getTarget();
        }

        if ($target instanceof Select && !$alias) {
            throw new LogicException("Sub-query join can't be used w/o alias.");
        }

        $noLeftAlias = false;

        if ($conditions instanceof WhereItem) {
            $conditions = $conditions->getRaw();

            $noLeftAlias = true;
        }

        if (empty($this->params[$type])) {
            $this->params[$type] = [];
        }

        if (is_array($target)) {
            $joinList = $target;

            foreach ($joinList as $item) {
                $this->params[$type][] = $item;
            }

            return $this;
        }

        if (
            is_null($alias) &&
            is_null($conditions) &&
            is_string($target) &&
            $this->hasJoinAliasInternal($type, $target)
        ) {
            return $this;
        }

        $params = [];

        if ($noLeftAlias) {
            $params['noLeftAlias'] = true;
        }

        if ($onlyMiddle) {
            $params['onlyMiddle'] = true;
        }

        if ($params !== []) {
            $this->params[$type][] = [$target, $alias, $conditions, $params];

            return $this;
        }

        if (is_null($alias) && is_null($conditions)) {
            $this->params[$type][] = $target;

            return $this;
        }

        if (is_null($conditions)) {
            $this->params[$type][] = [$target, $alias];

            return $this;
        }

        $this->params[$type][] = [$target, $alias, $conditions];

        return $this;
    }

    private function hasJoinAliasInternal(string $type, string $alias): bool
    {
        $joins = $this->params[$type] ?? [];

        if (in_array($alias, $joins)) {
            return true;
        }

        foreach ($joins as $item) {
            if (is_array($item) && count($item) > 1) {
                if ($item[1] === $alias) {
                    return true;
                }
            }
        }

        return false;
    }

    
    public function hasLeftJoinAlias(string $alias): bool
    {
        return $this->hasJoinAliasInternal('leftJoins', $alias);
    }

    
    public function hasJoinAlias(string $alias): bool
    {
        return $this->hasJoinAliasInternal('joins', $alias);
    }

    
    private function normalizeExpressionItemArray(array $itemList): array
    {
        $resultList = [];

        foreach ($itemList as $item) {
            if ($item instanceof Expression) {
                $resultList[] = $item->getValue();

                continue;
            }

            if (!is_array($item) || !count($item) || !$item[0] instanceof Expression) {
                
                $resultList[] = $item;

                continue;
            }

            $newItem = [$item[0]->getValue()];

            if (count($item) > 1) {
                $newItem[] = $item[1];
            }

            

            $resultList[] = $newItem;
        }

        return $resultList;
    }

    
    private function normalizeOrderExpressionItemArray(array $itemList, $direction): array
    {
        $resultList = [];

        foreach ($itemList as $item) {
            if (is_string($item)) {
                $resultList[] = [$item, $direction];

                continue;
            }

            if (is_int($item)) {
                $resultList[] = [(string) $item, $direction];

                continue;
            }

            if ($item instanceof Order) {
                $resultList[] = [
                    $item->getExpression()->getValue(),
                    $item->getDirection()
                ];

                continue;
            }

            if ($item instanceof Expression) {
                $resultList[] = [
                    $item->getValue(),
                    $direction
                ];

                continue;
            }

            if (!is_array($item) || !count($item)) {
                throw new RuntimeException("Bad order item.");
            }

            $itemValue = $item[0] instanceof Expression ?
                $item[0]->getValue() :
                $item[0];

            if (!is_string($itemValue) && !is_int($itemValue)) {
                throw new RuntimeException("Bad order item.");
            }

            $itemDirection = count($item) > 1 ? $item[1] : $direction;

            if (is_bool($itemDirection)) {
                $itemDirection = $itemDirection ?
                    Order::DESC :
                    Order::ASC;
            }

            $resultList[] = [$itemValue, $itemDirection];
        }

        return $resultList;
    }
}
