<?php


namespace Espo\Core\Mail\Account\Storage;


class Params
{
    
    private ?string $imapHandlerClassName;

    
    public function __construct(
        private ?string $host,
        private ?int $port,
        private ?string $username,
        private ?string $password,
        private ?string $security,
        ?string $imapHandlerClassName,
        private ?string $id,
        private ?string $userId,
        private ?string $emailAddress
    ) {
        $this->imapHandlerClassName = $imapHandlerClassName;
    }

    public static function createBuilder(): ParamsBuilder
    {
        return new ParamsBuilder();
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getUsername(): ?string
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

    
    public function getImapHandlerClassName(): ?string
    {
        return $this->imapHandlerClassName;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function withPassword(?string $password): self
    {
        $obj = clone $this;
        $obj->password = $password;

        return $obj;
    }

    
    public function withImapHandlerClassName(?string $imapHandlerClassName): self
    {
        $obj = clone $this;
        $obj->imapHandlerClassName = $imapHandlerClassName;

        return $obj;
    }
}
