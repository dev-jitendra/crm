<?php


namespace Espo\Core\Authentication\Jwt;

interface SignatureVerifierFactory
{
    public function create(string $algorithm): SignatureVerifier;
}
