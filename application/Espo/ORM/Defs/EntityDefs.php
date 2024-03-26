<?php


namespace Espo\ORM\Defs;

use RuntimeException;

class EntityDefs
{
    
    private array $data;
    private string $name;
    
    private $attributeCache = [];
    
    private $relationCache = [];
    
    private $indexCache = [];
    
    private $fieldCache = [];

    private function __construct()
    {}

    
    public static function fromRaw(array $raw, string $name): self
    {
        $obj = new self();
        $obj->data = $raw;
        $obj->name = $name;

        return $obj;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getAttributeNameList(): array
    {
        
        return array_keys($this->data['attributes'] ?? []);
    }

    
    public function getRelationNameList(): array
    {
        
        return array_keys($this->data['relations'] ?? []);
    }

    
    public function getIndexNameList(): array
    {
        
        return array_keys($this->data['indexes'] ?? []);
    }

    
    public function getFieldNameList(): array
    {
        
        return array_keys($this->data['fields'] ?? []);
    }

    
    public function getAttributeList(): array
    {
        $list = [];

        foreach ($this->getAttributeNameList() as $name) {
            $list[] = $this->getAttribute($name);
        }

        return $list;
    }

    
    public function getRelationList(): array
    {
        $list = [];

        foreach ($this->getRelationNameList() as $name) {
            $list[] = $this->getRelation($name);
        }

        return $list;
    }

    
    public function getIndexList(): array
    {
        $list = [];

        foreach ($this->getIndexNameList() as $name) {
            $list[] = $this->getIndex($name);
        }

        return $list;
    }

    
    public function getFieldList(): array
    {
        $list = [];

        foreach ($this->getFieldNameList() as $name) {
            $list[] = $this->getField($name);
        }

        return $list;
    }

    
    public function hasAttribute(string $name): bool
    {
        $this->cacheAttribute($name);

        return !is_null($this->attributeCache[$name]);
    }

    
    public function hasRelation(string $name): bool
    {
        $this->cacheRelation($name);

        return !is_null($this->relationCache[$name]);
    }

    
    public function hasIndex(string $name): bool
    {
        $this->cacheIndex($name);

        return !is_null($this->indexCache[$name]);
    }

    
    public function hasField(string $name): bool
    {
        $this->cacheField($name);

        return !is_null($this->fieldCache[$name]);
    }

    
    public function getAttribute(string $name): AttributeDefs
    {
        $this->cacheAttribute($name);

        if (!$this->hasAttribute($name)) {
            throw new RuntimeException("Attribute '{$name}' does not exist.");
        }

        
        return $this->attributeCache[$name];
    }

    
    public function getRelation(string $name): RelationDefs
    {
        $this->cacheRelation($name);

        if (!$this->hasRelation($name)) {
            throw new RuntimeException("Relation '{$name}' does not exist.");
        }

        
        return $this->relationCache[$name];
    }

    
    public function getIndex(string $name): IndexDefs
    {
        $this->cacheIndex($name);

        if (!$this->hasIndex($name)) {
            throw new RuntimeException("Index '{$name}' does not exist.");
        }

        
        return $this->indexCache[$name];
    }

    
    public function getField(string $name): FieldDefs
    {
        $this->cacheField($name);

        if (!$this->hasField($name)) {
            throw new RuntimeException("Field '{$name}' does not exist.");
        }

        
        return $this->fieldCache[$name];
    }

    
    public function tryGetAttribute(string $name): ?AttributeDefs
    {
        if (!$this->hasAttribute($name)) {
            return null;
        }

        return $this->getAttribute($name);
    }

    
    public function tryGetField(string $name): ?FieldDefs
    {
        if (!$this->hasField($name)) {
            return null;
        }

        return $this->getField($name);
    }

    
    public function tryGetRelation(string $name): ?RelationDefs
    {
        if (!$this->hasRelation($name)) {
            return null;
        }

        return $this->getRelation($name);
    }

    
    public function tryGetIndex(string $name): ?IndexDefs
    {
        if (!$this->hasIndex($name)) {
            return null;
        }

        return $this->getIndex($name);
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    
    public function getParam(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    private function cacheAttribute(string $name): void
    {
        if (array_key_exists($name, $this->attributeCache)) {
            return;
        }

        $this->attributeCache[$name] = $this->loadAttribute($name);
    }

    private function loadAttribute(string $name): ?AttributeDefs
    {
        $raw = $this->data['attributes'][$name] ?? $this->data['fields'][$name] ?? null;

        if (!$raw) {
            return null;
        }

        return AttributeDefs::fromRaw($raw, $name);
    }

    private function cacheRelation(string $name): void
    {
        if (array_key_exists($name, $this->relationCache)) {
            return;
        }

        $this->relationCache[$name] = $this->loadRelation($name);
    }

    private function loadRelation(string $name): ?RelationDefs
    {
        $raw = $this->data['relations'][$name] ?? null;

        if (!$raw) {
            return null;
        }

        return RelationDefs::fromRaw($raw, $name);
    }

    private function cacheIndex(string $name): void
    {
        if (array_key_exists($name, $this->indexCache)) {
            return;
        }

        $this->indexCache[$name] = $this->loadIndex($name);
    }

    private function loadIndex(string $name): ?IndexDefs
    {
        $raw = $this->data['indexes'][$name] ?? null;

        if (!$raw) {
            return null;
        }

        return IndexDefs::fromRaw($raw, $name);
    }

    private function cacheField(string $name): void
    {
        if (array_key_exists($name, $this->fieldCache)) {
            return;
        }

        $this->fieldCache[$name] = $this->loadField($name);
    }

    private function loadField(string $name): ?FieldDefs
    {
        $raw = $this->data['fields'][$name] ?? null;

        if (!$raw) {
            return null;
        }

        return FieldDefs::fromRaw($raw, $name);
    }
}
