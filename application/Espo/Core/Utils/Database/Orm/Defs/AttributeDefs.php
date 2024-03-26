<?php


namespace Espo\Core\Utils\Database\Orm\Defs;

use Espo\Core\Utils\Util;
use Espo\ORM\Type\AttributeType;


class AttributeDefs
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
        
        $value = $this->getParam('type');

        return $value;
    }

    
    public function withType(string $type): self
    {
        return $this->withParam('type', $type);
    }

    
    public function withDbType(string $dbType): self
    {
        return $this->withParam('dbType', $dbType);
    }

    
    public function withNotStorable(bool $value = true): self
    {
        return $this->withParam('notStorable', $value);
    }

    
    public function withLength(int $length): self
    {
        return $this->withParam('len', $length);
    }

    
    public function withDefault(mixed $value): self
    {
        return $this->withParam('default', $value);
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
