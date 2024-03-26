<?php


namespace Espo\Modules\Crm\Tools\Lead\Convert;

use Espo\Core\Utils\ObjectUtil;
use stdClass;
use UnexpectedValueException;


class Values
{
    
    private array $data = [];

    public static function create(): self
    {
        return new self();
    }

    public function has(string $entityType): bool
    {
        return array_key_exists($entityType, $this->data);
    }

    public function get(string $entityType): stdClass
    {
        $data = $this->data[$entityType] ?? null;

        if ($data === null) {
            throw new UnexpectedValueException();
        }

        return $data;
    }

    public function with(string $entityType, stdClass $data): self
    {
        $obj = clone $this;
        $obj->data[$entityType] = ObjectUtil::clone($data);

        return $obj;
    }

    public function getRaw(): stdClass
    {
        $data = (object) [];

        foreach ($this->data as $entityType => $item) {
            $data->$entityType = ObjectUtil::clone($item);
        }

        return $data;
    }
}
