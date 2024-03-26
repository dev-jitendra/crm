<?php


namespace Espo\ORM\Query\Part;


class Selection
{
    private function __construct(
        private Expression $expression,
        private ?string $alias = null
    ) {}

    public function getExpression(): Expression
    {
        return $this->expression;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public static function create(Expression $expression, ?string $alias = null): self
    {
        return new self($expression, $alias);
    }

    public static function fromString(string $expression): self
    {
        return self::create(
            Expression::create($expression)
        );
    }

    public function withAlias(?string $alias): self
    {
        $obj = clone $this;
        $obj->alias = $alias;

        return $obj;
    }
}
