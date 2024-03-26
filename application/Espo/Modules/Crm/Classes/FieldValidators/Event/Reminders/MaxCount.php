<?php


namespace Espo\Modules\Crm\Classes\FieldValidators\Event\Reminders;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\ORM\Entity;


class MaxCount implements Validator
{
    private const MAX_COUNT = 10;

    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        
        $list = $entity->get($field);

        if ($list === null) {
            return null;
        }

        if (count($list) > self::MAX_COUNT) {
            return Failure::create();
        }

        return null;
    }
}
