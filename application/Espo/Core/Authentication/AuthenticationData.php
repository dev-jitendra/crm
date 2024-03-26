<?php


namespace Espo\Core\Authentication;


class AuthenticationData
{
    private ?string $username;
    private ?string $password;
    private ?string $method;
    private bool $byTokenOnly = false;

    public function __construct(
        ?string $username = null,
        ?string $password = null,
        ?string $method = null
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->method = $method;
    }

    public static function create(): self
    {
        return new self();
    }

    
    public function getUsername(): ?string
    {
        return $this->username;
    }

    
    public function getPassword(): ?string
    {
        return $this->password;
    }

    
    public function getMethod(): ?string
    {
        return $this->method;
    }

    
    public function byTokenOnly(): bool
    {
        return $this->byTokenOnly;
    }

    public function withUsername(?string $username): self
    {
        $obj = clone $this;
        $obj->username = $username;

        return $obj;
    }

    public function withPassword(?string $password): self
    {
        $obj = clone $this;
        $obj->password = $password;

        return $obj;
    }

    public function withMethod(?string $method): self
    {
        $obj = clone $this;
        $obj->method = $method;

        return $obj;
    }

    public function withByTokenOnly(bool $byTokenOnly): self
    {
        $obj = clone $this;
        $obj->byTokenOnly = $byTokenOnly;

        return $obj;
    }
}
