<?php


namespace Espo\Core\FieldValidation\Validator;

use Espo\Core\Utils\ObjectUtil;
use stdClass;

class Data
{
    public function __construct(private stdClass $data)
    {}

    public function has(string $name): bool
    {
        return property_exists($this->data, $name);
    }

    public function get(string $name): mixed
    {
        return $this->getClonedData()->$name ?? null;
    }

    private function getClonedData(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }
}
