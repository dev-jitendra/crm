<?php


namespace Espo\Classes\FieldValidators\AuthenticationProvider;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\Core\Utils\Metadata;
use Espo\Entities\AuthenticationProvider;
use Espo\ORM\Entity;


class MethodValid implements Validator
{
    public function __construct(private Metadata $metadata) {}

    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        $value = $entity->get($field);

        if (!$value) {
            return Failure::create();
        }

        $isAvailable = $this->metadata->get(['authenticationMethods', $value, 'provider', 'isAvailable']);

        if (!$isAvailable) {
            return Failure::create();
        }

        return null;
    }
}
