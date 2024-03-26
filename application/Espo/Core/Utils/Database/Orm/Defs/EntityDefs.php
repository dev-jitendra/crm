<?php


namespace Espo\Core\Utils\Database\Orm\Defs;


class EntityDefs
{
    
    private array $attributes = [];
    
    private array $relations = [];
    
    private array $indexes = [];

    private function __construct() {}

    public static function create(): self
    {
        return new self();
    }

    public function withAttribute(AttributeDefs $attributeDefs): self
    {
        $obj = clone $this;
        $obj->attributes[$attributeDefs->getName()] = $attributeDefs;

        return $obj;
    }

    public function withRelation(RelationDefs $relationDefs): self
    {
        $obj = clone $this;
        $obj->relations[$relationDefs->getName()] = $relationDefs;

        return $obj;
    }

    public function withIndex(IndexDefs $index): self
    {
        $obj = clone $this;
        $obj->indexes[$index->getName()] = $index;

        return $obj;
    }

    public function withoutAttribute(string $name): self
    {
        $obj = clone $this;
        unset($obj->attributes[$name]);

        return $obj;
    }

    public function withoutRelation(string $name): self
    {
        $obj = clone $this;
        unset($obj->relations[$name]);

        return $obj;
    }

    public function withoutIndex(string $name): self
    {
        $obj = clone $this;
        unset($obj->indexes[$name]);

        return $obj;
    }

    public function getAttribute(string $name): ?AttributeDefs
    {
        return $this->attributes[$name] ?? null;
    }

    public function getRelation(string $name): ?RelationDefs
    {
        return $this->relations[$name] ?? null;
    }

    public function getIndex(string $name): ?IndexDefs
    {
        return $this->indexes[$name] ?? null;
    }

    
    public function toAssoc(): array
    {
        $data = [];

        if (count($this->attributes)) {
            $attributesData = [];

            foreach ($this->attributes as $name => $attributeDefs) {
                $attributesData[$name] = $attributeDefs->toAssoc();
            }

            $data['attributes'] = $attributesData;
        }

        if (count($this->relations)) {
            $relationsData = [];

            foreach ($this->relations as $name => $relationDefs) {
                $relationsData[$name] = $relationDefs->toAssoc();
            }

            $data['relations'] = $relationsData;
        }

        if (count($this->indexes)) {
            $indexesData = [];

            foreach ($this->indexes as $name => $indexDefs) {
                $indexesData[$name] = $indexDefs->toAssoc();
            }

            $data['indexes'] = $indexesData;
        }

        return $data;
    }
}
