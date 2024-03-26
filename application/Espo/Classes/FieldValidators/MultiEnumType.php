<?php


namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;

class MultiEnumType extends ArrayType
{
    public function checkNoEmptyString(Entity $entity, string $field, ?bool $validationValue): bool
    {
        return parent::checkNoEmptyString($entity, $field, true);
    }
}
