<?php


namespace Espo\Core\Api;


class AuthResult
{
    private bool $isResolved = false;
    private bool $isResolvedUseNoAuth = false;

    public static function createResolved(): self
    {
        $obj = new self();

        $obj->isResolved = true;

        return $obj;
    }

    public static function createResolvedUseNoAuth(): self
    {
        $obj = new self();

        $obj->isResolved = true;
        $obj->isResolvedUseNoAuth = true;

        return $obj;
    }

    public static function createNotResolved(): self
    {
        return new self();
    }

    
    public function isResolved(): bool
    {
        return $this->isResolved;
    }

    
    public function isResolvedUseNoAuth(): bool
    {
        return $this->isResolvedUseNoAuth;
    }
}
