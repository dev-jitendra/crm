<?php


namespace Espo\Tools\EntityManager\Rename;

class Result
{
    private bool $isFail = false;

    
    private ?string $failReason = null;

    public static function createSuccess(): self
    {
        return new self();
    }

    
    public static function createFail(string $reason): self
    {
        $obj = new self();

        $obj->isFail = true;
        $obj->failReason = $reason;

        return $obj;
    }

    public function isFail(): bool
    {
        return $this->isFail;
    }

    
    public function getFailReason(): ?string
    {
        return $this->failReason;
    }
}
