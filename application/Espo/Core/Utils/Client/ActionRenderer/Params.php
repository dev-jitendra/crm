<?php


namespace Espo\Core\Utils\Client\ActionRenderer;


class Params
{
    
    private ?array $data;
    private bool $initAuth = false;

    
    public function __construct(
        private string $controller,
        private string $action,
        ?array $data = null
    ) {
        $this->data = $data;
    }

    
    public static function create(string $controller, string $action, ?array $data = null): self
    {
        return new self($controller, $action, $data);
    }

    
    public function withData(array $data): self
    {
        $obj = clone $this;
        $obj->data = $data;

        return $obj;
    }

    public function withInitAuth(bool $initAuth = true): self
    {
        $obj = clone $this;
        $obj->initAuth = $initAuth;

        return $obj;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    
    public function getData(): ?array
    {
        return $this->data;
    }

    public function initAuth(): bool
    {
        return $this->initAuth;
    }
}
