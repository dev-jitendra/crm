<?php


namespace Espo\ORM\Query\Part;

use Espo\ORM\Query\Select;
use LogicException;
use RuntimeException;


class Join
{
    
    public const TYPE_TABLE = 0;
    
    public const TYPE_RELATION = 1;
    
    public const TYPE_SUB_QUERY = 3;

    private ?WhereItem $conditions = null;
    private bool $onlyMiddle = false;

    private function __construct(
        private string|Select $target,
        private ?string $alias = null
    ) {
        if ($target === '' || $alias === '') {
            throw new RuntimeException("Bad join.");
        }
    }

    
    public function getTarget(): string|Select
    {
        return $this->target;
    }

    
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    
    public function getConditions(): ?WhereItem
    {
        return $this->conditions;
    }

    
    public function isSubQuery(): bool
    {
        return !is_string($this->target);
    }

    
    public function isTable(): bool
    {
        return is_string($this->target) && $this->target[0] === ucfirst($this->target[0]);
    }

    
    public function isRelation(): bool
    {
        return !$this->isSubQuery() && !$this->isTable();
    }

    
    public function getType(): int
    {
        if ($this->isSubQuery()) {
            return self::TYPE_SUB_QUERY;
        }

        if ($this->isRelation()) {
            return self::TYPE_RELATION;
        }

        return self::TYPE_TABLE;
    }

    
    public function isOnlyMiddle(): bool
    {
        return $this->onlyMiddle;
    }

    
    public static function create(string|Select $target, ?string $alias = null): self
    {
        return new self($target, $alias);
    }

    
    public static function createWithTableTarget(string $table, ?string $alias = null): self
    {
        return self::create(ucfirst($table), $alias);
    }

    
    public static function createWithRelationTarget(string $relation, ?string $alias = null): self
    {
        return self::create(lcfirst($relation), $alias);
    }

    
    public static function createWithSubQuery(Select $subQuery, string $alias): self
    {
        return new self($subQuery, $alias);
    }

    
    public function withAlias(?string $alias): self
    {
        $obj = clone $this;
        $obj->alias = $alias;

        return $obj;
    }

    
    public function withConditions(?WhereItem $conditions): self
    {
        $obj = clone $this;
        $obj->conditions = $conditions;

        return $obj;
    }

    
    public function withOnlyMiddle(bool $onlyMiddle = true): self
    {
        if (!$this->isRelation()) {
            throw new LogicException("Only-middle is compatible only with relation joins.");
        }

        $obj = clone $this;
        $obj->onlyMiddle = $onlyMiddle;

        return $obj;
    }
}
