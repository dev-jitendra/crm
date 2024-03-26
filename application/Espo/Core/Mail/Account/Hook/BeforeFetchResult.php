<?php


namespace Espo\Core\Mail\Account\Hook;

class BeforeFetchResult
{
    private bool $toSkip = false;
    
    private array $data = [];

    public static function create(): self
    {
        return new self();
    }

    public function withToSkip(bool $toSkip = true): self
    {
        $obj = clone $this;
        $obj->toSkip = $toSkip;

        return $obj;
    }

    public function with(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->data[$name] = $value;

        return $obj;
    }

    public function toSkip(): bool
    {
        return $this->toSkip;
    }

    public function get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }
}
