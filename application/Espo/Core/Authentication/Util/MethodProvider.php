<?php


namespace Espo\Core\Authentication\Util;

use Espo\Core\ApplicationState;
use Espo\Core\Authentication\ConfigDataProvider;
use Espo\Core\Authentication\Logins\Espo;
use Espo\Core\ORM\EntityManagerProxy;
use Espo\Core\Utils\Metadata;
use Espo\Entities\AuthenticationProvider;
use Espo\Entities\Portal;
use RuntimeException;


class MethodProvider
{
    public function __construct(
        private EntityManagerProxy $entityManager,
        private ApplicationState $applicationState,
        private ConfigDataProvider $configDataProvider,
        private Metadata $metadata
    ) {}

    
    public function get(): string
    {
        if ($this->applicationState->isPortal()) {
            $method = $this->getForPortal($this->applicationState->getPortal());

            if ($method) {
                return $method;
            }

            return $this->getDefaultForPortal();
        }

        return $this->configDataProvider->getDefaultAuthenticationMethod();
    }

    
    public function getForPortal(Portal $portal): ?string
    {
        $providerId = $portal->getAuthenticationProvider()?->getId();

        if (!$providerId) {
            return null;
        }

        
        $provider = $this->entityManager->getEntityById(AuthenticationProvider::ENTITY_TYPE, $providerId);

        if (!$provider) {
            throw new RuntimeException("No authentication provider for portal.");
        }

        $method = $provider->getMethod();

        if (!$method) {
            throw new RuntimeException("No method in authentication provider.");
        }

        return $method;
    }

    
    private function getDefaultForPortal(): string
    {
        $method = $this->configDataProvider->getDefaultAuthenticationMethod();

        $allow = $this->metadata->get(['authenticationMethods', $method, 'portalDefault']);

        if (!$allow) {
            return Espo::NAME;
        }

        return $method;
    }
}
