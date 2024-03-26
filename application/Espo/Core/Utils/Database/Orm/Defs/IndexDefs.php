<?php


namespace Espo\Core\Utils\Database\Orm\Defs;

use Espo\Core\Utils\Util;


class IndexDefs
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

    public function withUnique(): self
    {
        $obj = clone $this;
        $obj->params['type'] = 'unique';

        return $obj;
    }

    public function withoutUnique(): self
    {
        $obj = clone $this;
        unset($obj->params['type']);

        return $obj;
    }

    public function withFlag(string $flag): self
    {
        $obj = clone $this;

        $flags = $obj->params['flags'] ?? [];

        if (!in_array($flag, $flags)) {
            $flags[] = $flag;
        }

        $obj->params['flags'] = $flags;

        return $obj;
    }

    public function withoutFlag(string $flag): self
    {
        $obj = clone $this;

        $flags = $obj->params['flags'] ?? [];

        $index = array_search($flag, $flags, true);

        if ($index !== -1) {
            unset($flags[$index]);
            $flags = array_values($flags);
        }

        $obj->params['flags'] = $flags;

        if ($flags === []) {
            unset($obj->params['flags']);
        }

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
