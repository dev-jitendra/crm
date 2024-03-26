<?php


namespace Espo\Modules\Crm\Classes\FieldValidators\Event\Reminders;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\Modules\Crm\Entities\Reminder;
use Espo\ORM\Entity;
use stdClass;


class Valid implements Validator
{
    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        
        $list = $entity->get($field);

        if ($list === null) {
            return null;
        }

        foreach ($list as $item) {
            if (!$item instanceof stdClass) {
                return Failure::create();
            }

            $seconds = $item->seconds ?? null;
            $type = $item->type ?? null;

            if (!is_int($seconds)) {
                return Failure::create();
            }

            if ($seconds < 0) {
                return Failure::create();
            }

            if (!in_array($type, [Reminder::TYPE_POPUP, Reminder::TYPE_EMAIL])) {
                return Failure::create();
            }
        }

        return null;
    }
}
