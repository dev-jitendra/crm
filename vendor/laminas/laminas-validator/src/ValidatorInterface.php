<?php

namespace Laminas\Validator;


interface ValidatorInterface
{
    
    public function isValid($value);

    
    public function getMessages();
}
