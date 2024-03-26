<?php


namespace Espo\ORM\Defs;

use Espo\ORM\Metadata;

use RuntimeException;

class DefsData
{
    
    private array $cache = [];

    public function __construct(private Metadata $metadata)
    {}

    public function clearCache(): void
    {
        $this->cache = [];
    }

    
    public function getEntityTypeList(): array
    {
        return $this->metadata->getEntityTypeList();
    }

    public function hasEntity(string $name): bool
    {
        $this->cacheEntity($name);

        return !is_null($this->cache[$name]);
    }

    public function getEntity(string $name): EntityDefs
    {
        $this->cacheEntity($name);

        if (!$this->hasEntity($name)) {
            throw new RuntimeException("Entity type '{$name}' does not exist.");
        }

        
        return $this->cache[$name];
    }

    private function cacheEntity(string $name): void
    {
        if (array_key_exists($name, $this->cache)) {
            return;
        }

        $this->cache[$name] = $this->loadEntity($name);
    }

    private function loadEntity(string $name): ?EntityDefs
    {
        $raw = $this->metadata->get($name) ?? null;

        if (!$raw) {
            return null;
        }

        return EntityDefs::fromRaw($raw, $name);
    }
}
