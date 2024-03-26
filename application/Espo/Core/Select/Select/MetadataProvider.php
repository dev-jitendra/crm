<?php


namespace Espo\Core\Select\Select;

use Espo\Core\Utils\Metadata;
use Espo\ORM\EntityManager;

class MetadataProvider
{
    public function __construct(private Metadata $metadata, private EntityManager $entityManager)
    {}

    public function getDefaultOrderBy(string $entityType): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'orderBy'
        ]) ?? null;
    }

    
    public function getSelectAttributesDependencyMap(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'selectAttributesDependencyMap'
        ]) ?? null;
    }

    
    public function getAclPortalAttributeList(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'aclPortalAttributeList'
        ]) ?? null;
    }

    
    public function getAclAttributeList(string $entityType): ?array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'aclAttributeList'
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

    public function isAttributeNotStorable(string $entityType, string $attribute): bool
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->isNotStorable();
    }

    public function getAttributeType(string $entityType, string $attribute): string
    {
        return $this->entityManager
            ->getMetadata()
            ->getDefs()
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->getType();
    }
}
