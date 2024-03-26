<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\Join;

use RuntimeException;

trait SelectingTrait
{
    
    public function getOrder(): array
    {
        return array_map(
            function ($item) {
                if (is_array($item) && count($item)) {
                    $itemValue = is_int($item[0]) ? (string) $item[0] : $item[0];

                    return Order::fromString($itemValue)
                        ->withDirection($item[1] ?? Order::ASC);
                }

                if (is_string($item)) {
                    return Order::fromString($item);
                }

                throw new RuntimeException("Bad order item.");
            },
            $this->params['orderBy'] ?? []
        );
    }

    
    public function getWhere(): ?WhereClause
    {
        $whereClause = $this->params['whereClause'] ?? null;

        if ($whereClause === null || $whereClause === []) {
            return null;
        }

        $where = WhereClause::fromRaw($whereClause);

        if (!$where instanceof WhereClause) {
            throw new RuntimeException();
        }

        return $where;
    }

    
    public function getJoins(): array
    {
        return array_map(
            function ($item) {
                if (is_string($item)) {
                    $item = [$item];
                }

                $conditions = isset($item[2]) ?
                    WhereClause::fromRaw($item[2]) :
                    null;

                return Join::create($item[0])
                    ->withAlias($item[1] ?? null)
                    ->withConditions($conditions);
            },
            $this->params['joins'] ?? []
        );
    }

    
    public function getLeftJoins(): array
    {
        return array_map(
            function ($item) {
                if (is_string($item)) {
                    $item = [$item];
                }

                $conditions = isset($item[2]) ?
                    WhereClause::fromRaw($item[2]) :
                    null;

                return Join::create($item[0])
                    ->withAlias($item[1] ?? null)
                    ->withConditions($conditions);
            },
            $this->params['leftJoins'] ?? []
        );
    }

    
    private static function validateRawParamsSelecting(array $params): void
    {
    }
}
