<?php


namespace Espo\Core\Select\Text;

use Espo\Core\Utils\Metadata;

use Espo\ORM\Defs;

class MetadataProvider
{
    private Defs $ormDefs;

    public function __construct(private Metadata $metadata, Defs $ormDefs)
    {
        $this->ormDefs = $ormDefs;
    }

    public function getFullTextSearchOrderType(string $entityType): ?string
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'fullTextSearchOrderType'
        ]);
    }

    
    public function getTextFilterAttributeList(string $entityType): ?array
    {
        return $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'textFilterFields'
        ]);
    }

    public function isFieldNotStorable(string $entityType, string $field): bool
    {
        return (bool) $this->metadata->get([
            'entityDefs', $entityType, 'fields', $field, 'notStorable'
        ]);
    }

    public function isFullTextSearchSupportedForField(string $entityType, string $field): bool
    {
        $fieldType = $this->metadata->get([
            'entityDefs', $entityType, 'fields', $field, 'type'
        ]);

        return (bool) $this->metadata->get([
            'fields', $fieldType, 'fullTextSearch'
        ]);
    }

    public function hasFullTextSearch(string $entityType): bool
    {
        return (bool) $this->metadata->get([
            'entityDefs', $entityType, 'collection', 'fullTextSearch'
        ]);
    }

    
    public function getUseContainsAttributeList(string $entityType): array
    {
        return $this->metadata->get([
            'selectDefs', $entityType, 'textFilterUseContainsAttributeList'
        ]) ?? [];
    }

    
    public function getFullTextSearchColumnList(string $entityType): ?array
    {
        return $this->ormDefs
            ->getEntity($entityType)
            ->getParam('fullTextSearchColumnList');
    }

    public function getRelationType(string $entityType, string $link): string
    {
        return $this->ormDefs
            ->getEntity($entityType)
            ->getRelation($link)
            ->getType();
    }

    public function getAttributeType(string $entityType, string $attribute): string
    {
        return $this->ormDefs
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->getType();
    }

    public function getFieldType(string $entityType, string $field): ?string
    {
        $entityDefs = $this->ormDefs->getEntity($entityType);

        if (!$entityDefs->hasField($field)) {
            return null;
        }

        return $entityDefs->getField($field)->getType();
    }

    public function getRelationEntityType(string $entityType, string $link): ?string
    {
        $relationDefs = $this->ormDefs
            ->getEntity($entityType)
            ->getRelation($link);

        if (!$relationDefs->hasForeignEntityType()) {
            return null;
        }

        return $relationDefs->getForeignEntityType();
    }

    public function getAttributeRelationParam(string $entityType, string $attribute): ?string
    {
        return $this->ormDefs
            ->getEntity($entityType)
            ->getAttribute($attribute)
            ->getParam('relation');
    }
}
