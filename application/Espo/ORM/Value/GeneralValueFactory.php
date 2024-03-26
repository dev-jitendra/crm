<?php


namespace Espo\ORM\Value;

use Espo\ORM\Entity;

use RuntimeException;

class GeneralValueFactory
{
    
    private array $factoryCache = [];

    public function __construct(private ValueFactoryFactory $valueFactoryFactory)
    {}

    
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        $factory = $this->getValueFactory($entity->getEntityType(), $field);

        if (!$factory) {
            return false;
        }

        return $factory->isCreatableFromEntity($entity, $field);
    }

    
    public function createFromEntity(Entity $entity, string $field): object
    {
        $factory = $this->getValueFactory($entity->getEntityType(), $field);

        if (!$factory) {
            $entityType = $entity->getEntityType();

            throw new RuntimeException("No value-object factory for '{$entityType}.{$field}'.");
        }

        
        return $factory->createFromEntity($entity, $field);
    }

    private function getValueFactory(string $entityType, string $field): ?ValueFactory
    {
        $key = $entityType . '_' . $field;

        if (!array_key_exists($key, $this->factoryCache)) {
            $this->factoryCache[$key] = $this->getValueFactoryNoCache($entityType, $field);
        }

        return $this->factoryCache[$key];
    }

    private function getValueFactoryNoCache(string $entityType, string $field): ?ValueFactory
    {
        if (!$this->valueFactoryFactory->isCreatable($entityType, $field)) {
            return null;
        }

        return $this->valueFactoryFactory->create($entityType, $field);
    }
}
