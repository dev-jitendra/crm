<?php


namespace Espo\ORM\Query;

class LockTableBuilder implements Builder
{
    use BaseBuilderTrait;

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): LockTable
    {
        return LockTable::fromRaw($this->params);
    }

    
    public function clone(LockTable $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function table(string $entityType): self
    {
        $this->params['table'] = $entityType;

        return $this;
    }

    
    public function inShareMode(): self
    {
        $this->params['mode'] = LockTable::MODE_SHARE;

        return $this;
    }

    
    public function inExclusiveMode(): self
    {
        $this->params['mode'] = LockTable::MODE_EXCLUSIVE;

        return $this;
    }
}
