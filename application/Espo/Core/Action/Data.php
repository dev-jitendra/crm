<?php


namespace Espo\Core\Action;

use Espo\Core\Utils\ObjectUtil;

use stdClass;

class Data
{
    private stdClass $data;

    private function __construct()
    {
        $this->data = (object) [];
    }

    public function getRaw(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }

    
    public function get(string $name)
    {
        return $this->getRaw()->$name ?? null;
    }

    
    public function has(string $name): bool
    {
        return property_exists($this->data, $name);
    }

    public static function fromRaw(stdClass $data): self
    {
        $obj = new self();

        $obj->data = $data;

        return $obj;
    }

    
    public function with(string $name, $value): self
    {
        $obj = clone $this;

        $obj->data->$name = $value;

        return $obj;
    }

    public function __clone()
    {
        $this->data = ObjectUtil::clone($this->data);
    }
}
