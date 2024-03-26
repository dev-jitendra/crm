<?php


namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;
use stdClass;

class IntType
{
    public function checkRequired(Entity $entity, string $field): bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    
    public function checkMax(Entity $entity, string $field, $validationValue): bool
    {
        if (!$this->isNotEmpty($entity, $field)) {
            return true;
        }

        if ($entity->get($field) > $validationValue) {
            return false;
        }

        return true;
    }

    
    public function checkMin(Entity $entity, string $field, $validationValue): bool
    {
        if (!$this->isNotEmpty($entity, $field)) {
            return true;
        }

        if ($entity->get($field) < $validationValue) {
            return false;
        }

        return true;
    }

    
    public function rawCheckValid(stdClass $data, string $field): bool
    {
        if (!isset($data->$field)) {
            return true;
        }

        $value = $data->$field;

        if ($value === '') {
            return true;
        }

        if (is_numeric($value)) {
            return true;
        }

        return false;
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== null;
    }
}
