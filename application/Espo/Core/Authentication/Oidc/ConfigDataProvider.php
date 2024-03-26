<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\ApplicationState;
use Espo\Core\ORM\EntityManagerProxy;
use Espo\Core\Utils\Config;
use Espo\Entities\AuthenticationProvider;
use stdClass;

class ConfigDataProvider
{
    private const JWKS_CACHE_PERIOD = '10 minutes';

    private Config|AuthenticationProvider $object;

    public function __construct(
        private Config $config,
        private ApplicationState $applicationState,
        private EntityManagerProxy $entityManager
    ) {
        $this->object = $this->getAuthenticationProvider() ?? $this->config;
    }

    private function isAuthenticationProvider(): bool
    {
        return $this->object instanceof AuthenticationProvider;
    }

    private function getAuthenticationProvider(): ?AuthenticationProvider
    {
        if (!$this->applicationState->isPortal()) {
            return null;
        }

        $link = $this->applicationState->getPortal()->getAuthenticationProvider();

        if (!$link) {
            return null;
        }

        
        return $this->entityManager->getEntityById(AuthenticationProvider::ENTITY_TYPE, $link->getId());
    }

    public function getSiteUrl(): string
    {
        $siteUrl = $this->isAuthenticationProvider() ?
            $this->applicationState->getPortal()->getUrl() :
            $this->config->get('siteUrl');

        return rtrim($siteUrl, '/');
    }

    public function getRedirectUri(): string
    {
        return $this->getSiteUrl() . '/oauth-callback.php';
    }

    public function getClientId(): ?string
    {
        return $this->object->get('oidcClientId');
    }

    public function getClientSecret(): ?string
    {
        return $this->object->get('oidcClientSecret');
    }

    public function getAuthorizationEndpoint(): ?string
    {
        return $this->object->get('oidcAuthorizationEndpoint');
    }

    public function getTokenEndpoint(): ?string
    {
        return $this->object->get('oidcTokenEndpoint');
    }

    public function getJwksEndpoint(): ?string
    {
        return $this->object->get('oidcJwksEndpoint');
    }

    
    public function getJwtSignatureAlgorithmList(): array
    {
        return $this->object->get('oidcJwtSignatureAlgorithmList') ?? [];
    }

    
    public function getScopes(): array
    {
        
        return $this->object->get('oidcScopes') ?? [];
    }

    public function getLogoutUrl(): ?string
    {
        return $this->object->get('oidcLogoutUrl');
    }

    public function getUsernameClaim(): ?string
    {
        return $this->object->get('oidcUsernameClaim');
    }

    public function createUser(): bool
    {
        return (bool) $this->object->get('oidcCreateUser');
    }

    public function sync(): bool
    {
        return (bool) $this->object->get('oidcSync');
    }

    public function syncTeams(): bool
    {
        if ($this->isAuthenticationProvider()) {
            return false;
        }

        return (bool) $this->config->get('oidcSyncTeams');
    }

    public function fallback(): bool
    {
        if ($this->isAuthenticationProvider()) {
            return false;
        }

        return (bool) $this->config->get('oidcFallback');
    }

    public function allowRegularUserFallback(): bool
    {
        if ($this->isAuthenticationProvider()) {
            return false;
        }

        return (bool) $this->config->get('oidcAllowRegularUserFallback');
    }

    public function allowAdminUser(): bool
    {
        if ($this->isAuthenticationProvider()) {
            return false;
        }

        return (bool) $this->config->get('oidcAllowAdminUser');
    }

    public function getGroupClaim(): ?string
    {
        if ($this->isAuthenticationProvider()) {
            return null;
        }

        return $this->config->get('oidcGroupClaim');
    }

    
    public function getTeamIds(): ?array
    {
        if ($this->isAuthenticationProvider()) {
            return null;
        }

        return $this->config->get('oidcTeamsIds') ?? [];
    }

    public function getTeamColumns(): ?stdClass
    {
        if ($this->isAuthenticationProvider()) {
            return null;
        }

        return $this->config->get('oidcTeamsColumns') ?? (object) [];
    }

    public function getAuthorizationPrompt(): string
    {
        return $this->config->get('oidcAuthorizationPrompt') ?? 'consent';
    }

    public function getAuthorizationMaxAge(): ?int
    {
        return $this->config->get('oidcAuthorizationMaxAge');
    }

    public function getJwksCachePeriod(): string
    {
        return $this->config->get('oidcJwksCachePeriod') ?? self::JWKS_CACHE_PERIOD;
    }
}
