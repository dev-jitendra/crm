<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class AuthLogRecord extends Entity
{
    public const ENTITY_TYPE = 'AuthLogRecord';

    public const DENIAL_REASON_CREDENTIALS = 'CREDENTIALS';
    public const DENIAL_REASON_INACTIVE_USER = 'INACTIVE_USER';
    public const DENIAL_REASON_IS_PORTAL_USER = 'IS_PORTAL_USER';
    public const DENIAL_REASON_IS_NOT_PORTAL_USER = 'IS_NOT_PORTAL_USER';
    public const DENIAL_REASON_USER_IS_NOT_IN_PORTAL = 'USER_IS_NOT_IN_PORTAL';
    public const DENIAL_REASON_IS_SYSTEM_USER = 'IS_SYSTEM_USER';

    public function setUsername(?string $username): self
    {
        $this->set('username', $username);

        return $this;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->set('ipAddress', $ipAddress);

        return $this;
    }

    public function setRequestMethod(string $requestMethod): self
    {
        $this->set('requestMethod', $requestMethod);

        return $this;
    }

    public function setRequestUrl(string $requestUrl): self
    {
        $this->set('requestUrl', $requestUrl);

        return $this;
    }

    public function setAuthenticationMethod(?string $authenticationMethod): self
    {
        $this->set('authenticationMethod', $authenticationMethod);

        return $this;
    }

    public function setRequestTime(?float $requestTime): self
    {
        $this->set('requestTime', $requestTime);

        return $this;
    }

    public function setUserId(?string $userId): self
    {
        $this->set('userId', $userId);

        return $this;
    }

    public function setPortalId(?string $portalId): self
    {
        $this->set('portalId', $portalId);

        return $this;
    }

    public function setAuthTokenId(?string $authTokenId): self
    {
        $this->set('authTokenId', $authTokenId);

        return $this;
    }

    public function setIsDenied(bool $isDenied = true): self
    {
        $this->set('isDenied', $isDenied);

        return $this;
    }

    public function setDenialReason(?string $denialReason): self
    {
        $this->set('denialReason', $denialReason);

        return $this;
    }
}
