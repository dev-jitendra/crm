<?php


namespace Espo\Tools\Oidc;

use Espo\Core\Authentication\Jwt\Exceptions\Invalid;
use Espo\Core\Authentication\Oidc\ConfigDataProvider;
use Espo\Core\Authentication\Oidc\Login as OidcLogin;
use Espo\Core\Authentication\Oidc\BackchannelLogout;
use Espo\Core\Authentication\Util\MethodProvider;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Json;

class Service
{
    public function __construct(
        private BackchannelLogout $backchannelLogout,
        private MethodProvider $methodProvider,
        private ConfigDataProvider $configDataProvider
    ) {}

    
    public function getAuthorizationData(): array
    {
        if ($this->methodProvider->get() !== OidcLogin::NAME) {
            throw new Forbidden();
        }

        $clientId = $this->configDataProvider->getClientId();
        $endpoint = $this->configDataProvider->getAuthorizationEndpoint();
        $scopes = $this->configDataProvider->getScopes();
        $groupClaim = $this->configDataProvider->getGroupClaim();
        $redirectUri = $this->configDataProvider->getRedirectUri();

        if (!$clientId) {
            throw new Error("No client ID.");
        }

        if (!$endpoint) {
            throw new Error("No authorization endpoint.");
        }

        array_unshift($scopes, 'openid');

        $claims = null;

        if ($groupClaim) {
            $claims = Json::encode([
                'id_token' => [
                    $groupClaim => ['essential' => true],
                ],
            ]);
        }

        
        $prompt = $this->configDataProvider->getAuthorizationPrompt();
        $maxAge = $this->configDataProvider->getAuthorizationMaxAge();

        return [
            'clientId' => $clientId,
            'endpoint' => $endpoint,
            'redirectUri' => $redirectUri,
            'scopes' => $scopes,
            'claims' => $claims,
            'prompt' => $prompt,
            'maxAge' => $maxAge,
        ];
    }

    
    public function backchannelLogout(string $rawToken): void
    {
        if ($this->methodProvider->get() !== OidcLogin::NAME) {
            throw new Forbidden();
        }

        try {
            $this->backchannelLogout->logout($rawToken);
        }
        catch (Invalid $e) {
            throw new Forbidden("OIDC logout: Invalid JWT. " . $e->getMessage());
        }
    }
}
