<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\Expression;

use RuntimeException;


class Select implements SelectingQuery
{
    use SelectingTrait;
    use BaseTrait;

    public const ORDER_ASC = Order::ASC;
    public const ORDER_DESC = Order::DESC;

    
    public function getFrom(): ?string
    {
        return $this->params['from'] ?? null;
    }

    
    public function getFromAlias(): ?string
    {
        return $this->params['fromAlias'] ?? null;
    }

    
    public function getFromQuery(): ?SelectingQuery
    {
        return $this->params['fromQuery'] ?? null;
    }

    
    public function getOffset(): ?int
    {
        return $this->params['offset'] ?? null;
    }

    
    public function getLimit(): ?int
    {
        return $this->params['limit'] ?? null;
    }

    
    public function getUseIndex(): array
    {
        return $this->params['useIndex'] ?? [];
    }

    
    public function getSelect(): array
    {
        return array_map(
            function ($item) {
                if (is_array($item) && count($item)) {
                    return Selection::fromString($item[0])
                        ->withAlias($item[1] ?? null);
                }

                if (is_string($item)) {
                    return Selection::fromString($item);
                }

                throw new RuntimeException("Bad select item.");
            },
            $this->params['select'] ?? []
        );
    }

    
    public function isDistinct(): bool
    {
        return $this->params['distinct'] ?? false;
    }

    
    public function isForShare(): bool
    {
        return $this->params['forShare'] ?? false;
    }

    
    public function isForUpdate(): bool
    {
        return $this->params['forUpdate'] ?? false;
    }

    
    public function getGroup(): array
    {
        return array_map(
            function (string $item) {
                return Expression::create($item);
            },
            $this->params['groupBy'] ?? []
        );
    }

    
    public function getHaving(): ?WhereClause
    {
        $havingClause = $this->params['havingClause'] ?? null;

        if ($havingClause === null || $havingClause === []) {
            return null;
        }

        $having = WhereClause::fromRaw($havingClause);

        if (!$having instanceof WhereClause) {
            throw new RuntimeException();
        }

        return $having;
    }

    
    private function validateRawParams(array $params): void
    {
        $this->validateRawParamsSelecting($params);

        if (
            (
                !empty($params['joins']) ||
                !empty($params['leftJoins']) ||
                !empty($params['whereClause']) ||
                !empty($params['orderBy'])
            )
            &&
            empty($params['from']) && empty($params['fromQuery'])
        ) {
            throw new RuntimeException("Select params: Missing 'from'.");
        }
    }
}
