<?php


namespace Espo\ORM\Query\Part;

use RuntimeException;


class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    private Expression $expression;
    private bool $isDesc = false;

    private function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    
    public function getExpression(): Expression
    {
        return $this->expression;
    }

    public function isDesc(): bool
    {
        return $this->isDesc;
    }

    
    public function getDirection(): string
    {
        return $this->isDesc ? self::DESC : self::ASC;
    }

    
    public static function create(Expression $expression): self
    {
        return new self($expression);
    }

    
    public static function fromString(string $expression): self
    {
        return self::create(
            Expression::create($expression)
        );
    }

    
    public static function createByPositionInList(Expression $expression, array $list): self
    {
        $orderExpression = Expression::positionInList($expression, array_reverse($list));

        return self::create($orderExpression)->withDesc();
    }

    
    public function withAsc(): self
    {
        $obj = clone $this;
        $obj->isDesc = false;

        return $obj;
    }

    
    public function withDesc(): self
    {
        $obj = clone $this;
        $obj->isDesc = true;

        return $obj;
    }

    
    public function withDirection(string $direction): self
    {
        $obj = clone $this;
        $obj->isDesc = strtoupper($direction) === self::DESC;

        if (!in_array(strtoupper($direction), [self::DESC, self::ASC])) {
            throw new RuntimeException("Bad order direction.");
        }

        return $obj;
    }

    
    public function withReverseDirection(): self
    {
        $obj = clone $this;
        $obj->isDesc = !$this->isDesc;

        return $obj;
    }
}
