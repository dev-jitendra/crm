<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\WhereItem;

use InvalidArgumentException;
use RuntimeException;

class SelectBuilder implements Builder
{
    use SelectingBuilderTrait;

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): Select
    {
        return Select::fromRaw($this->params);
    }

    
    public function clone(Select $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function from(string $entityType, ?string $alias = null): self
    {
        if (isset($this->params['from']) && $entityType !== $this->params['from']) {
            throw new RuntimeException("Method 'from' can be called only once.");
        }

        if (isset($this->params['fromQuery'])) {
            throw new RuntimeException("Method 'from' can't be if 'fromQuery' is set.");
        }

        $this->params['from'] = $entityType;

        if ($alias) {
            $this->params['fromAlias'] = $alias;
        }

        return $this;
    }

    
    public function fromQuery(SelectingQuery $query, string $alias): self
    {
        if (isset($this->params['from'])) {
            throw new RuntimeException("Method 'fromQuery' can be called only once.");
        }

        if (isset($this->params['fromQuery'])) {
            throw new RuntimeException("Method 'fromQuery' can't be if 'from' is set.");
        }

        if ($alias === '') {
            throw new RuntimeException("Alias can't be empty.");
        }

        $this->params['fromQuery'] = $query;
        $this->params['fromAlias'] = $alias;

        return $this;
    }

    
    public function distinct(): self
    {
        $this->params['distinct'] = true;

        return $this;
    }

    
    public function limit(?int $offset = null, ?int $limit = null): self
    {
        $this->params['offset'] = $offset;
        $this->params['limit'] = $limit;

        return $this;
    }

    
    public function select($select, ?string $alias = null): self
    {
        

        if (is_array($select)) {
            $this->params['select'] = $this->normalizeSelectExpressionArray($select);

            return $this;
        }

        if ($select instanceof Expression) {
            $select = $select->getValue();
        }
        else if ($select instanceof Selection) {
            $alias = $alias ?? $select->getAlias();
            $select = $select->getExpression()->getValue();
        }

        if (is_string($select)) {
            $this->params['select'] = $this->params['select'] ?? [];

            $this->params['select'][] = $alias ?
                [$select, $alias] :
                $select;

            return $this;
        }

        throw new InvalidArgumentException();
    }

    
    public function group($groupBy): self
    {
        

        if (is_array($groupBy)) {
            $this->params['groupBy'] = $this->normalizeExpressionItemArray($groupBy);

            return $this;
        }

        if ($groupBy instanceof Expression) {
            $groupBy = $groupBy->getValue();
        }

        if (is_string($groupBy)) {
            $this->params['groupBy'] = $this->params['groupBy'] ?? [];

            $this->params['groupBy'][] = $groupBy;

            return $this;
        }

        throw new InvalidArgumentException();
    }

    
    public function groupBy($groupBy): self
    {
        return $this->group($groupBy);
    }

    
    public function useIndex(string $index): self
    {
        $this->params['useIndex'] = $this->params['useIndex'] ?? [];

        $this->params['useIndex'][] = $index;

        return $this;
    }

    
    public function having($clause, $value = null): self
    {
        $this->applyWhereClause('havingClause', $clause, $value);

        return $this;
    }

    
    public function forShare(): self
    {
        if (isset($this->params['forUpdate'])) {
            throw new RuntimeException("Can't use two lock modes together.");
        }

        $this->params['forShare'] = true;

        return $this;
    }

    
    public function forUpdate(): self
    {
        if (isset($this->params['forShare'])) {
            throw new RuntimeException("Can't use two lock modes together.");
        }

        $this->params['forUpdate'] = true;

        return $this;
    }

    
    public function withDeleted(): self
    {
        $this->params['withDeleted'] = true;

        return $this;
    }

    
    private function normalizeSelectExpressionArray(array $itemList): array
    {
        $resultList = [];

        foreach ($itemList as $item) {
            if ($item instanceof Expression) {
                $resultList[] = $item->getValue();

                continue;
            }

            if ($item instanceof Selection) {
                $resultList[] = $item->getAlias() ?
                    [$item->getExpression()->getValue(), $item->getAlias()] :
                    [$item->getExpression()->getValue()];

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
}
