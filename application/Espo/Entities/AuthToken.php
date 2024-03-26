<?php


namespace Espo\Entities;

use Espo\Core\Authentication\AuthToken\AuthToken as AuthTokenInterface;
use Espo\Core\Field\DateTime;
use Espo\Core\ORM\Entity as BaseEntity;

class AuthToken extends BaseEntity implements AuthTokenInterface
{
    public const ENTITY_TYPE = 'AuthToken';

    public function getToken(): string
    {
        return $this->get('token');
    }

    public function getUserId(): string
    {
        return $this->get('userId');
    }

    public function getPortalId(): ?string
    {
        return $this->get('portalId');
    }

    public function getSecret(): ?string
    {
        return $this->get('secret');
    }

    public function isActive(): bool
    {
        return $this->get('isActive');
    }

    public function getHash(): ?string
    {
        return $this->get('hash');
    }

    public function setIsActive(bool $isActive): self
    {
        $this->set('isActive', $isActive);

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->set('userId', $userId);

        return $this;
    }

    public function setPortalId(?string $portalId): self
    {
        $this->set('portalId', $portalId);

        return $this;
    }

    public function setHash(?string $hash): self
    {
        $this->set('hash', $hash);

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->set('token', $token);

        return $this;
    }

    public function setSecret(string $secret): self
    {
        $this->set('secret', $secret);

        return $this;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->set('ipAddress', $ipAddress);

        return $this;
    }

    public function setLastAccessNow(): self
    {
        $this->set('lastAccess', DateTime::createNow()->toString());

        return $this;
    }
}
