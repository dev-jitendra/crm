<?php


namespace Espo\Core\Authentication\AuthToken;


interface Manager
{
    
    public function get(string $token): ?AuthToken;

    
    public function create(Data $data): AuthToken;

    
    public function inactivate(AuthToken $authToken): void;

    
    public function renew(AuthToken $authToken): void;
}
