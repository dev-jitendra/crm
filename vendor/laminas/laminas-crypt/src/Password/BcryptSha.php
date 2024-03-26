<?php

namespace Laminas\Crypt\Password;

use Laminas\Crypt\Hash;


class BcryptSha extends Bcrypt
{
    
    public function create($password)
    {
        return parent::create(Hash::compute('sha256', $password));
    }

    
    public function verify($password, $hash)
    {
        return parent::verify(Hash::compute('sha256', $password), $hash);
    }
}
