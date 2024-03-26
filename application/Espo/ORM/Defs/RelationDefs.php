<?php


namespace Espo\ORM\Defs;

use Espo\ORM\Entity;

use RuntimeException;


class RelationDefs
{
    
    private array $data;
    private string $name;

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

    
    public function getType(): string
    {
        $type = $this->data['type'] ?? null;

        if ($type === null) {
            throw new RuntimeException("Relation '{$this->name}' has no type.");
        }

        return $type;
    }

    
    public function isManyToMany(): bool
    {
        return $this->getType() === Entity::MANY_MANY;
    }

    
    public function isHasMany(): bool
    {
        return $this->getType() === Entity::HAS_MANY;
    }

    
    public function isHasOne(): bool
    {
        return $this->getType() === Entity::HAS_ONE;
    }

    
    public function isHasChildren(): bool
    {
        return $this->getType() === Entity::HAS_CHILDREN;
    }

    
    public function isBelongsTo(): bool
    {
        return $this->getType() === Entity::BELONGS_TO;
    }

    
    public function isBelongsToParent(): bool
    {
        return $this->getType() === Entity::BELONGS_TO_PARENT;
    }

    
    public function hasForeignEntityType(): bool
    {
        return isset($this->data['entity']);
    }

    
    public function getForeignEntityType(): string
    {
        if (!$this->hasForeignEntityType()) {
            throw new RuntimeException("No 'entity' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['entity'];
    }

    
    public function hasForeignRelationName(): bool
    {
        return isset($this->data['foreign']);
    }

    
    public function getForeignRelationName(): string
    {
        if (!$this->hasForeignRelationName()) {
            throw new RuntimeException("No 'foreign' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['foreign'];
    }

    
    public function hasForeignKey(): bool
    {
        return isset($this->data['foreignKey']);
    }

    
    public function getForeignKey(): string
    {
        if (!$this->hasForeignKey()) {
            throw new RuntimeException("No 'foreignKey' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['foreignKey'];
    }

    
    public function hasKey(): bool
    {
        return isset($this->data['key']);
    }

    
    public function getKey(): string
    {
        if (!$this->hasKey()) {
            throw new RuntimeException("No 'key' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['key'];
    }

    
    public function hasMidKey(): bool
    {
        return !is_null($this->data['midKeys'][0] ?? null);
    }

    
    public function getMidKey(): string
    {
        if (!$this->hasMidKey()) {
            throw new RuntimeException("No 'midKey' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['midKeys'][0];
    }

    
    public function hasForeignMidKey(): bool
    {
        return !is_null($this->data['midKeys'][1] ?? null);
    }

    
    public function getForeignMidKey(): string
    {
        if (!$this->hasForeignMidKey()) {
            throw new RuntimeException("No 'foreignMidKey' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['midKeys'][1];
    }

    
    public function hasRelationshipName(): bool
    {
        return isset($this->data['relationName']);
    }

    
    public function getRelationshipName(): string
    {
        if (!$this->hasRelationshipName()) {
            throw new RuntimeException("No 'relationName' parameter defined in the relation '{$this->name}'.");
        }

        return $this->data['relationName'];
    }

    
    public function getIndexList(): array
    {
        if ($this->getType() !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't get indexes.");
        }

        $list = [];

        foreach (($this->data['indexes'] ?? []) as $name => $item) {
            $list[] = IndexDefs::fromRaw($item, $name);
        }

        return $list;
    }

    
    public function getConditions(): array
    {
        if ($this->getType() !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't get conditions for non many-many relationship.");
        }

        return $this->getParam('conditions') ?? [];
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    
    public function getParam(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }
}
