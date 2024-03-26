<?php


namespace Espo\Tools\EntityManager;

class CreateParams
{
    
    public function __construct(
        private bool $forceCreate = false,
        private array $replaceData = []
    ) {}

    public function forceCreate(): bool
    {
        return $this->forceCreate;
    }

    
    public function getReplaceData(): array
    {
        return $this->replaceData;
    }
}
