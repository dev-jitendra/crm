<?php


namespace Espo\Core;

use Espo\Entities\Portal as PortalEntity;
use Espo\Entities\User as UserEntity;

use LogicException;


class ApplicationState
{
    private const KEY_USER = 'user';
    private const KEY_PORTAL = 'portal';

    public function __construct(private Container $container)
    {}

    
    public function isPortal(): bool
    {
        return $this->container->has(self::KEY_PORTAL);
    }

    
    public function getPortalId(): string
    {
        if (!$this->isPortal()) {
            throw new LogicException("Can't get portal ID for non-portal application.");
        }

        return $this->getPortal()->getId();
    }

    
    public function getPortal(): PortalEntity
    {
        if (!$this->isPortal()) {
            throw new LogicException("Can't get portal for non-portal application.");
        }

        
        return $this->container->get(self::KEY_PORTAL);
    }

    
    public function hasUser(): bool
    {
        return $this->container->has(self::KEY_USER);
    }

    
    public function getUser(): UserEntity
    {
        if (!$this->hasUser()) {
            throw new LogicException("User is not yet available.");
        }

        
        return $this->container->get(self::KEY_USER);
    }

    
    public function getUserId(): string
    {
        return $this->getUser()->getId();
    }

    
    public function isLogged(): bool
    {
        if (!$this->container->has(self::KEY_USER)) {
            return false;
        }

        if ($this->getUser()->isSystem()) {
            return false;
        }

        return true;
    }

    
    public function isAdmin(): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        return $this->getUser()->isAdmin();
    }


    
    public function isApi(): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        return $this->getUser()->isApi();
    }
}
