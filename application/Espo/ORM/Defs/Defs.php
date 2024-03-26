<?php


namespace Espo\ORM\Defs;

use RuntimeException;


class Defs
{
    public function __construct(private DefsData $data)
    {}

    
    public function getEntityTypeList(): array
    {
        return $this->data->getEntityTypeList();
    }

    
    public function getEntityList(): array
    {
        $list = [];

        foreach ($this->getEntityTypeList() as $name) {
            $list[] = $this->getEntity($name);
        }

        return $list;
    }

    
    public function hasEntity(string $entityType): bool
    {
        return $this->data->hasEntity($entityType);
    }

    
    public function getEntity(string $entityType): EntityDefs
    {
        if (!$this->hasEntity($entityType)) {
            throw new RuntimeException("Entity type '{$entityType}' does not exist.");
        }

        return $this->data->getEntity($entityType);
    }

    
    public function tryGetEntity(string $entityType): ?EntityDefs
    {
        if (!$this->hasEntity($entityType)) {
            return null;
        }

        return $this->getEntity($entityType);
    }
}
