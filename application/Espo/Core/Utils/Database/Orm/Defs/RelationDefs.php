<?php


namespace Espo\Core\Utils\Database\Orm\Defs;

use Espo\Core\Utils\Util;
use Espo\ORM\Type\RelationType;

class RelationDefs
{
    
    private array $params = [];

    private function __construct(private string $name) {}

    public static function create(string $name): self
    {
        return new self($name);
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getType(): ?string
    {
        
        return $this->getParam('type');
    }

    
    public function withType(string $type): self
    {
        return $this->withParam('type', $type);
    }

    
    public function withForeignEntityType(string $entityType): self
    {
        return $this->withParam('entity', $entityType);
    }

    
    public function getForeignEntityType(): ?string
    {
        return $this->getParam('entity');
    }

    
    public function withForeignRelationName(?string $name): self
    {
        return $this->withParam('foreign', $name);
    }

    
    public function getForeignRelationName(): ?string
    {
        return $this->getParam('foreign');
    }

    
    public function withRelationshipName(string $name): self
    {
        return $this->withParam('relationName', $name);
    }

    
    public function getRelationshipName(): ?string
    {
        return $this->getParam('relationName');
    }

    
    public function withKey(string $key): self
    {
        return $this->withParam('key', $key);
    }

    
    public function getKey(): ?string
    {
        return $this->getParam('key');
    }

    
    public function withForeignKey(string $foreignKey): self
    {
        return $this->withParam('foreignKey', $foreignKey);
    }

    
    public function getForeignKey(): ?string
    {
        return $this->getParam('foreignKey');
    }

    
    public function withMidKeys(string $midKey, string $foreignMidKey): self
    {
        return $this->withParam('midKeys', [$midKey, $foreignMidKey]);
    }

    
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    
    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    
    public function withParam(string $name, mixed $value): self
    {
        $obj = clone $this;
        $obj->params[$name] = $value;

        return $obj;
    }

    
    public function withoutParam(string $name): self
    {
        $obj = clone $this;
        unset($obj->params[$name]);

        return $obj;
    }

    
    public function withConditions(array $conditions): self
    {
        $obj = clone $this;

        return $obj->withParam('conditions', $conditions);
    }

    
    public function withAdditionalColumn(AttributeDefs $attributeDefs): self
    {
        $obj = clone $this;

        
        $list = $obj->getParam('additionalColumns') ?? [];

        $list[$attributeDefs->getName()] = $attributeDefs->toAssoc();

        return $obj->withParam('additionalColumns', $list);
    }

    
    public function withParamsMerged(array $params): self
    {
        $obj = clone $this;

        
        $params = Util::merge($this->params, $params);

        $obj->params = $params;

        return $obj;
    }

    
    public function toAssoc(): array
    {
        return $this->params;
    }
}
