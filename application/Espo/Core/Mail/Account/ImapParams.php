<?php


namespace Espo\Core\Mail\Account;


class ImapParams
{
    public function __construct(
        private string $host,
        private int $port,
        private string $username,
        private ?string $password,
        private ?string $security
    ) {}

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSecurity(): ?string
    {
        return $this->security;
    }
}
