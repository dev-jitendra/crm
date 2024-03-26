<?php


namespace Espo\Core\Authentication\AuthToken;

use RuntimeException;


class Data
{
    private string $userId;
    private ?string $portalId = null;
    private ?string $hash = null;
    private ?string $ipAddress = null;
    private bool $createSecret = false;

    private function __construct()
    {}

    
    public function getUserId(): string
    {
        return $this->userId;
    }

    
    public function getPortalId(): ?string
    {
        return $this->portalId;
    }

    
    public function getHash(): ?string
    {
        return $this->hash;
    }

    
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    
    public function toCreateSecret(): bool
    {
        return $this->createSecret;
    }

    
    public static function create(array $data): self
    {
        $obj = new self();

        $userId = $data['userId'] ?? null;

        if (!$userId) {
            throw new RuntimeException("No user ID.");
        }

        $obj->userId = $userId;
        $obj->portalId = $data['portalId'] ?? null;
        $obj->hash = $data['hash'] ?? null;
        $obj->ipAddress = $data['ipAddress'] ?? null;
        $obj->createSecret = $data['createSecret'] ?? false;

        return $obj;
    }
}
