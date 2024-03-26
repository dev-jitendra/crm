<?php


namespace Espo\ORM\Query;

class InsertBuilder implements Builder
{
    use BaseBuilderTrait;

    
    protected $params = [];

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): Insert
    {
        return Insert::fromRaw($this->params);
    }

    
    public function clone(Insert $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function into(string $entityType): self
    {
        $this->params['into'] = $entityType;

        return $this;
    }

    
    public function columns(array $columns): self
    {
        $this->params['columns'] = $columns;

        return $this;
    }

    
    public function values(array $values): self
    {
        $this->params['values'] = $values;

        return $this;
    }

    
    public function updateSet(array $updateSet): self
    {
        $this->params['updateSet'] = $updateSet;

        return $this;
    }

    
    public function valuesQuery(SelectingQuery $query): self
    {
        $this->params['valuesQuery'] = $query;

        return $this;
    }
}
