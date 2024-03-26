<?php


namespace Espo\ORM;

use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;
use stdClass;


interface Entity
{
    public const ID = AttributeType::ID;
    public const VARCHAR = AttributeType::VARCHAR;
    public const INT = AttributeType::INT;
    public const FLOAT = AttributeType::FLOAT;
    public const TEXT = AttributeType::TEXT;
    public const BOOL = AttributeType::BOOL;
    public const FOREIGN_ID = AttributeType::FOREIGN_ID;
    public const FOREIGN = AttributeType::FOREIGN;
    public const FOREIGN_TYPE = AttributeType::FOREIGN_TYPE;
    public const DATE = AttributeType::DATE;
    public const DATETIME = AttributeType::DATETIME;
    public const JSON_ARRAY = AttributeType::JSON_ARRAY;
    public const JSON_OBJECT = AttributeType::JSON_OBJECT;
    public const PASSWORD = AttributeType::PASSWORD;

    public const MANY_MANY = RelationType::MANY_MANY;
    public const HAS_MANY = RelationType::HAS_MANY;
    public const BELONGS_TO = RelationType::BELONGS_TO;
    public const HAS_ONE = RelationType::HAS_ONE;
    public const BELONGS_TO_PARENT = RelationType::BELONGS_TO_PARENT;
    public const HAS_CHILDREN = RelationType::HAS_CHILDREN;

    
    public function getId(): string;

    
    public function hasId(): bool;

    
    public function reset(): void;

    
    public function set($attribute, $value = null): void;

    
    public function setMultiple(array|stdClass $valueMap): void;

    
    public function get(string $attribute);

    
    public function has(string $attribute): bool;

    
    public function clear(string $attribute): void;

    
    public function getEntityType(): string;

    
    public function getAttributeList(): array;

    
    public function getRelationList(): array;

    
    public function hasAttribute(string $attribute): bool;

    
    public function hasRelation(string $relation): bool;

    
    public function getAttributeType(string $attribute): ?string;

    
    public function getRelationType(string $relation): ?string;

    
    public function isNew(): bool;

    
    public function setAsFetched(): void;

    
    public function isFetched(): bool;

    
    public function isAttributeChanged(string $name): bool;

    
    public function getFetched(string $attribute);

    
    public function hasFetched(string $attribute): bool;

    
    public function setFetched(string $attribute, $value): void;

    
    public function getValueMap(): stdClass;

    
    public function setAsNotNew(): void;

    
    public function updateFetchedValues(): void;
}
