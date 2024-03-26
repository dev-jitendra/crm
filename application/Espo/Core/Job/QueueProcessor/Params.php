<?php


namespace Espo\Core\Job\QueueProcessor;

class Params
{
    private bool $useProcessPool = false;
    private bool $noLock = false;
    private ?string $queue = null;
    private ?string $group = null;
    private int $limit = 0;

    public function withUseProcessPool(bool $useProcessPool): self
    {
        $obj = clone $this;
        $obj->useProcessPool = $useProcessPool;

        return $obj;
    }

    public function withNoLock(bool $noLock): self
    {
        $obj = clone $this;
        $obj->noLock = $noLock;

        return $obj;
    }

    public function withQueue(?string $queue): self
    {
        $obj = clone $this;
        $obj->queue = $queue;

        return $obj;
    }

    public function withGroup(?string $group): self
    {
        $obj = clone $this;
        $obj->group = $group;

        return $obj;
    }

    public function withLimit(int $limit): self
    {
        $obj = clone $this;
        $obj->limit = $limit;

        return $obj;
    }

    public function useProcessPool(): bool
    {
        return $this->useProcessPool;
    }

    public function noLock(): bool
    {
        return $this->noLock;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public static function create(): self
    {
        return new self();
    }
}
