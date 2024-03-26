<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;


trait ProxyArrayAccessToProperties
{
    private function formatPropertyName(string $offset): string
    {
        return str_replace('_', '', lcfirst(ucwords($offset, '_')));
    }

    
    public function offsetExists($offset): bool
    {
        $property = $this->formatPropertyName((string) $offset);

        return isset($this->{$property});
    }

    
    public function offsetGet($offset)
    {
        $property = $this->formatPropertyName((string) $offset);

        return $this->{$property};
    }

    
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }

    
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }
}
