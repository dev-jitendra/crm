<?php


namespace Espo\Core\Authentication\AuthToken;


interface AuthToken
{
    
    public function getToken(): string;

    
    public function getUserId(): string;

    
    public function getPortalId(): ?string;

    
    public function getSecret(): ?string;

    
    public function isActive(): bool;

    
    public function getHash(): ?string;
}
