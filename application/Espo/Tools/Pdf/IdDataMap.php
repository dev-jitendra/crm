<?php


namespace Espo\Tools\Pdf;

class IdDataMap
{
    
    private $map = [];

    public function set(string $id, Data $data): void
    {
        $this->map[$id] = $data;
    }

    public function get(string $id): ?Data
    {
        return $this->map[$id] ?? null;
    }

    public static function create(): self
    {
        return new self();
    }
}
