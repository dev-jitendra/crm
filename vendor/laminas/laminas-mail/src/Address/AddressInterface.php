<?php

namespace Laminas\Mail\Address;

interface AddressInterface
{
    
    public function getEmail();

    
    public function getName();

    
    public function toString();
}
