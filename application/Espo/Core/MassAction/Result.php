<?php


namespace Espo\Core\MassAction;

use RuntimeException;


class Result
{
    private ?int $count = null;
    
    private $ids = null;

    
    public function __construct(?int $count, ?array $ids = null)
    {
        $this->count = $count;
        $this->ids = $ids;
    }

    public function hasIds(): bool
    {
        return $this->ids !== null;
    }

    public function hasCount(): bool
    {
        return $this->count !== null;
    }

    
    public function getIds(): array
    {
        if (!$this->hasIds()) {
            throw new RuntimeException("No IDs.");
        }

        
        return $this->ids;
    }

    public function getCount(): int
    {
        if (!$this->hasCount()) {
            throw new RuntimeException("No count.");
        }

        
        return $this->count;
    }

    public function withNoIds(): self
    {
        return new self($this->count);
    }

    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['count'] ?? null,
            $data['ids'] ?? null
        );
    }
}
