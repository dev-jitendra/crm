<?php


namespace Espo\Core\Authentication\Jwt;

interface SignatureVerifier
{
    public function verify(Token $token): bool;
}
