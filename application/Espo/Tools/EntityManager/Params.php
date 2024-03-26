<?php


namespace Espo\Tools\EntityManager;

class Params
{
    
    public function __construct(
        private string $name,
        private ?string $type,
        private array $params
    ) {}

    public function get(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
