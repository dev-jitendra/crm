<?php


namespace Espo\Core\Di;

use Espo\Core\FieldValidation\FieldValidationManager;

interface FieldValidationManagerAware
{
    public function setFieldValidationManager(FieldValidationManager $fieldValidationManager): void;
}
