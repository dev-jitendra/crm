<?php


namespace Espo\ORM\Repository\Option\Traits;

trait Options
{
    
    private array $options;

    
    private function __construct(array $options)
    {
        $this->options = $options;
    }

    
    public static function fromAssoc(array $options): self
    {
        return new self($options);
    }

    
    public function get(string $option): mixed
    {
        return $this->options[$option] ?? null;
    }

    
    public function has(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    
    public function with(string $option, mixed $value): self
    {
        $obj = clone $this;
        $obj->options[$option] = $value;

        return $obj;
    }

    
    public function without(string $option): self
    {
        $obj = clone $this;
        unset($obj->options[$option]);

        return $obj;
    }

    
    public function toAssoc(): array
    {
        return $this->options;
    }
}
