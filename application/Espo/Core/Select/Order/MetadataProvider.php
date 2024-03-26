<?php


namespace Espo\Core\Select\Order;

use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;

class MetadataProvider
{
    public function __construct(private Metadata $metadata, private EntityManager $entityManager)
    {}

    public function getFieldType(string $entityType, string $field): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'fields', $field, 'type'
        ]) ?? null;
    }

    public function getDefaultOrderBy(string $entityType): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'orderBy'
        ]) ?? null;
    }

    public function getDefaultOrder(string $entityType): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'order'
        ]) ?? null;
    }

    public function hasAttribute(string $entityType, string $attribute): bool
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->hasAttribute($attribute);
    }

    public function isAttributeParamUniqueTrue(string $entityType, string $attribute): bool
    {
        return (bool) $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->getParam('unique');
    }
}
