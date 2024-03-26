<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\Authentication\AuthToken\AuthToken;
use Espo\Core\Authentication\Logout as LogoutInterface;
use Espo\Core\Authentication\Logout\Params;
use Espo\Core\Authentication\Logout\Result;

class Logout implements LogoutInterface
{
    public function __construct(
        private ConfigDataProvider $configDataProvider
    ) {}

    public function logout(AuthToken $authToken, Params $params): Result
    {
        $url = $this->configDataProvider->getLogoutUrl();
        $clientId = $this->configDataProvider->getClientId() ?? '';
        $siteUrl = $this->configDataProvider->getSiteUrl();

        if ($url) {
            $url = str_replace('{clientId}', urlencode($clientId), $url);
            $url = str_replace('{siteUrl}', urlencode($siteUrl), $url);
        }

        

        return Result::create()->withRedirectUrl($url);
    }
}
