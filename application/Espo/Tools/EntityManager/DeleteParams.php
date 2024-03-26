<?php


namespace Espo\Tools\EntityManager;

class DeleteParams
{
    public function __construct(
        private bool $forceRemove = false
    ) {}

    public function forceRemove(): bool
    {
        return $this->forceRemove;
    }
}
