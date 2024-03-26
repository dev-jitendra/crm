<?php


namespace Espo\Core\Di;

use Espo\Core\FieldValidation\FieldValidationManager;

trait FieldValidationManagerSetter
{
    
    protected $fieldValidationManager;

    public function setFieldValidationManager(FieldValidationManager $fieldValidationManager): void
    {
        $this->fieldValidationManager = $fieldValidationManager;
    }
}
