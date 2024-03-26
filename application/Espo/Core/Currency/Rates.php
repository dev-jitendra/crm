<?php


namespace Espo\Core\Currency;

use RuntimeException;


class Rates
{
    
    private array $data = [];

    private function __construct(private ?string $baseCode = null)
    {}

    
    public static function create(?string $baseCode = null): self
    {
        return new self($baseCode);
    }

    
    public function getBase(): string
    {
        if ($this->baseCode === null) {
            throw new RuntimeException("Base code is not set.");
        }

        return $this->baseCode;
    }

    
    public function withRate(string $code, float $value): self
    {
        $obj = clone $this;
        $obj->data[$code] = $value;

        return $obj;
    }

    
    public function hasRate(string $code): bool
    {
        return array_key_exists($code, $this->data);
    }

    
    public function getRate(string $code): float
    {
        if (!$this->hasRate($code)) {
            throw new RuntimeException("No currency rate for '{$code}'.");
        }

        return $this->data[$code];
    }

    
    public function toAssoc(): array
    {
        return array_merge(
            $this->data,
            [$this->getBase() => 1.0]
        );
    }

    
    public static function fromAssoc(array $data, ?string $baseCode = null): self
    {
        $obj = new self($baseCode);
        $obj->data = $data;

        return $obj;
    }

    
    public function fromArray(array $data, ?string $baseCode = null): self
    {
        return self::fromAssoc($data, $baseCode);
    }
}
