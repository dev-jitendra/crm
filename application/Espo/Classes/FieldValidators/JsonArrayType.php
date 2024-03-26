<?php


namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;

use stdClass;

class JsonArrayType
{
    public function rawCheckArray(stdClass $data, string $field): bool
    {
        if (isset($data->$field) && !is_array($data->$field)) {
            return false;
        }

        return true;
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        if (!$entity->has($field) || $entity->get($field) === null) {
            return false;
        }

        $list = $entity->get($field);

        if (!is_array($list)) {
            return false;
        }

        if (count($list)) {
            return true;
        }

        return false;
    }
}
