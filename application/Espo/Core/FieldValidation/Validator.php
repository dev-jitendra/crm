<?php


namespace Espo\Core\FieldValidation;

use Espo\ORM\Entity;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;


interface Validator
{
    
    public function validate(Entity $entity, string $field, Data $data): ?Failure;
}
