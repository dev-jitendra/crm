<?php


namespace Espo\Core\Authentication;

use Espo\Core\Authentication\Login\MetadataParams;
use Espo\Core\Authentication\Logins\Espo;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

use RuntimeException;

class ConfigDataProvider
{
    private const FAILED_ATTEMPTS_PERIOD =  '60 seconds';
    private const MAX_FAILED_ATTEMPT_NUMBER = 10;

    private Config $config;
    private Metadata $metadata;

    public function __construct(Config $config, Metadata $metadata)
    {
        $this->config = $config;
        $this->metadata = $metadata;
    }

    
    public function getFailedAttemptsPeriod(): string
    {
        return $this->config->get('authFailedAttemptsPeriod', self::FAILED_ATTEMPTS_PERIOD);
    }

    
    public function getMaxFailedAttemptNumber(): int
    {
        return $this->config->get('authMaxFailedAttemptNumber', self::MAX_FAILED_ATTEMPT_NUMBER);
    }

    
    public function isAuthTokenSecretDisabled(): bool
    {
        return (bool) $this->config->get('authTokenSecretDisabled');
    }

    
    public function isMaintenanceMode(): bool
    {
        return (bool) $this->config->get('maintenanceMode');
    }

    
    public function isTwoFactorEnabled(): bool
    {
        return (bool) $this->config->get('auth2FA');
    }

    
    public function getTwoFactorMethodList(): array
    {
        return $this->config->get('auth2FAMethodList') ?? [];
    }

    
    public function preventConcurrentAuthToken(): bool
    {
        return (bool) $this->config->get('authTokenPreventConcurrent');
    }

    
    public function getDefaultAuthenticationMethod(): string
    {
        return $this->config->get('authenticationMethod', Espo::NAME);
    }

    
    public function authenticationMethodIsApi(string $authenticationMethod): bool
    {
        return (bool) $this->metadata->get(['authenticationMethods', $authenticationMethod, 'api']);
    }

    public function isAnotherUserDisabled(): bool
    {
        return (bool) $this->config->get('authAnotherUserDisabled');
    }

    public function isAuthLogDisabled(): bool
    {
        return (bool) $this->config->get('authLogDisabled');
    }

    public function isApiUserAuthLogDisabled(): bool
    {
        return (bool) $this->config->get('authApiUserLogDisabled');
    }

    
    public function getLoginMetadataParamsList(): array
    {
        $list = [];

        
        $data = $this->metadata->get(['authenticationMethods']) ?? [];

        foreach ($data as $method => $item) {
            $list[] = MetadataParams::fromRaw($method, $item);
        }

        return $list;
    }

    public function getMethodLoginMetadataParams(string $method): MetadataParams
    {
        
        $data = $this->metadata->get(['authenticationMethods', $method]);

        if ($data === null) {
            throw new RuntimeException();
        }

        return MetadataParams::fromRaw($method, $data);
    }
}
