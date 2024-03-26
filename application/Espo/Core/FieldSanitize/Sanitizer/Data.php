<?php


namespace Espo\Core\FieldSanitize\Sanitizer;

use stdClass;


class Data
{
    public function __construct(private stdClass $data)
    {}

    
    public function get(string $attribute): mixed
    {
        return $this->data->$attribute ?? null;
    }


    
    public function has(string $attribute): bool
    {
        return property_exists($this->data, $attribute);
    }

    
    public function set(string $attribute, mixed $value): self
    {
        $this->data->$attribute = $value;

        return $this;
    }

    
    public function clear(string $attribute): self
    {
        unset($this->data->$attribute);

        return $this;
    }
}
