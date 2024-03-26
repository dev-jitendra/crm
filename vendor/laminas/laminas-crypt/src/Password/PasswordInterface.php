<?php

namespace Laminas\Crypt\Password;

interface PasswordInterface
{
    
    public function create($password);

    
    public function verify($password, $hash);
}
