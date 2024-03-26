<?php


namespace Espo\Core\Authentication\Ldap;

class ClientFactory
{
    
    public function create(array $options): Client
    {
        return new Client($options);
    }
}
