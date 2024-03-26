<?php


namespace Espo\ORM\Query;

use Espo\ORM\Query\Part\Expression;
use RuntimeException;

class UpdateBuilder implements Builder
{
    use SelectingBuilderTrait;

    
    public static function create(): self
    {
        return new self();
    }

    
    public function build(): Update
    {
        return Update::fromRaw($this->params);
    }

    
    public function clone(Update $query): self
    {
        $this->cloneInternal($query);

        return $this;
    }

    
    public function in(string $entityType): self
    {
        if (isset($this->params['from'])) {
            throw new RuntimeException("Method 'in' can be called only once.");
        }

        $this->params['from'] = $entityType;

        return $this;
    }

    
    public function set(array $set): self
    {
        $modified = [];

        foreach ($set as $key => $value) {
            if (!$value instanceof Expression) {
                $modified[$key] = $value;

                continue;
            }

            $newKey = rtrim($key, ':')  . ':';

            $modified[$newKey] = $value->getValue();
        }

        $this->params['set'] = $modified;

        return $this;
    }

    
    public function limit(?int $limit = null): self
    {
        $this->params['limit'] = $limit;

        return $this;
    }
}
