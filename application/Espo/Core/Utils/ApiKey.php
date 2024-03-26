<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\Config\ConfigWriter;

class ApiKey
{
    public function __construct(
        private Config $config,
        private ConfigWriter $configWriter)
    {}

    public static function hash(string $secretKey, string $string = ''): string
    {
        return hash_hmac('sha256', $string, $secretKey, true);
    }

    public function getSecretKeyForUserId(string $id): ?string
    {
        $apiSecretKeys = $this->config->get('apiSecretKeys');

        if (!$apiSecretKeys) {
            return null;
        }

        if (!is_object($apiSecretKeys)) {
            return null;
        }

        if (!isset($apiSecretKeys->$id)) {
            return null;
        }

        return $apiSecretKeys->$id;
    }

    public function storeSecretKeyForUserId(string $id, string $secretKey): void
    {
        $apiSecretKeys = $this->config->get('apiSecretKeys');

        if (!is_object($apiSecretKeys)) {
            $apiSecretKeys = (object) [];
        }

        $apiSecretKeys->$id = $secretKey;

        $this->configWriter->set('apiSecretKeys', $apiSecretKeys);
        $this->configWriter->save();
    }

    public function removeSecretKeyForUserId(string $id): void
    {
        $apiSecretKeys = $this->config->get('apiSecretKeys');

        if (!is_object($apiSecretKeys)) {
            $apiSecretKeys = (object) [];
        }

        unset($apiSecretKeys->$id);

        $this->configWriter->set('apiSecretKeys', $apiSecretKeys);
        $this->configWriter->save();
    }
}
