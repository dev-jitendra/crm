<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Field\Date;
use Espo\ORM\Entity;

use Exception;

class DateType
{
    public function checkRequired(Entity $entity, string $field): bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== null;
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        
        $value = $entity->get($field);

        if ($value === null) {
            return true;
        }

        try {
            Date::fromString($value);
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }
}
