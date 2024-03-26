<?php


namespace Espo\Core\Authentication\Jwt;

interface Key
{
    public function getKid(): string;

    public function getKty(): string;

    public function getAlg(): ?string;
}
