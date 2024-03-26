<?php


namespace Espo\Classes\FieldValidators\Email;

use Espo\Entities\Email;
use Espo\ORM\Entity;

class EmailAddresses
{
    
    public function checkRequired(Entity $entity, string $field): bool
    {
        if ($entity->getStatus() === Email::STATUS_DRAFT) {
            return true;
        }

        return $this->isNotEmpty($entity, $field);
    }

    private function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}
