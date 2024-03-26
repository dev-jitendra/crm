<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\Date;
use Espo\ORM\Entity;
use Exception;

class DatetimeOptionalType extends DatetimeType
{
    public function checkRequired(Entity $entity, string $field): bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        if ($entity->has($field) && $entity->get($field) !== null) {
            return true;
        }

        if ($entity->has($field . 'Date') && $entity->get($field . 'Date') !== null) {
            return true;
        }

        return false;
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        
        $dateValue = $entity->get($field  . 'Date');

        if ($dateValue !== null) {
            try {
                Date::fromString($dateValue);
            }
            catch (Exception $e) {
                return false;
            }
        }

        
        $value = $entity->get($field);

        if ($value !== null) {
            try {
                DateTime::fromString($value);
            }
            catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
