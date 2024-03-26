<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Field\DateTime;
use Espo\ORM\Entity;

use Exception;

class DatetimeType extends DateType
{
    public function checkValid(Entity $entity, string $field): bool
    {
        
        $value = $entity->get($field);

        if ($value === null) {
            return true;
        }

        try {
            DateTime::fromString($value);
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }
}
