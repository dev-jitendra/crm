<?php


namespace Espo\ORM\Query;

use RuntimeException;

class DeleteBuilder implements Builder
{
    use SelectingBuilderTrait;

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): Delete
    {
        return Delete::fromRaw($this->params);
    }

    
    public function clone(Delete $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function from(string $entityType, ?string $alias = null): self
    {
        if (isset($this->params['from'])) {
            throw new RuntimeException("Method 'from' can be called only once.");
        }

        $this->params['from'] = $entityType;
        $this->params['fromAlias'] = $alias;

        return $this;
    }

    
    public function limit(?int $limit = null): self
    {
        $this->params['limit'] = $limit;

        return $this;
    }
}
