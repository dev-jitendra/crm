<?php


namespace Espo\Core\Formula\Parser\Ast;


class Node
{
    
    public function __construct(private string $type, private array $childNodes)
    {}

    public function getType(): string
    {
        return $this->type;
    }

    
    public function getChildNodes(): array
    {
        return $this->childNodes;
    }
}
