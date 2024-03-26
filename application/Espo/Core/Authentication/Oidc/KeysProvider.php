<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\Authentication\Jwt\Exceptions\UnsupportedKey;
use Espo\Core\Authentication\Jwt\Key;
use Espo\Core\Authentication\Jwt\KeyFactory;
use Espo\Core\Field\DateTime;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\Json;
use Espo\Core\Utils\Log;
use JsonException;
use RuntimeException;
use stdClass;

class KeysProvider
{
    private const CACHE_KEY = 'oidcJwks';
    private const REQUEST_TIMEOUT = 10;

    public function __construct(
        private DataCache $dataCache,
        private Config $config,
        private ConfigDataProvider $configDataProvider,
        private KeyFactory $factory,
        private Log $log
    ) {}

    
    public function get(): array
    {
        $list = [];

        $rawKeys = $this->getRaw();

        foreach ($rawKeys as $raw) {
            try {
                $list[] = $this->factory->create($raw);
            }
            catch (UnsupportedKey $e) {
                $this->log->debug("OIDC: Unsupported key " . print_r($raw, true));
            }
        }

        return $list;
    }

    
    private function getRaw(): array
    {
        $raw = $this->getRawFromCache();

        if (!$raw) {
            $raw = $this->load();

            $this->storeRawToCache($raw);
        }

        return $raw;
    }

    
    private function load(): array
    {
        $endpoint = $this->configDataProvider->getJwksEndpoint();

        if (!$endpoint) {
            throw new RuntimeException("JSON Web Key Set endpoint not specified in settings.");
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        ]);

        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($response === false) {
            $response = '';
        }

        if ($error) {
            throw new RuntimeException("OIDC: JWKS request error. Status: {$status}.");
        }

        $parsedResponse = null;

        try {
            $parsedResponse = Json::decode($response);
        }
        catch (JsonException) {}

        if (!$parsedResponse instanceof stdClass || !isset($parsedResponse->keys)) {
            throw new RuntimeException("OIDC: JWKS bad response.");
        }

        return $parsedResponse->keys;
    }

    
    private function getRawFromCache(): ?array
    {
        if (!$this->config->get('useCache')) {
            return null;
        }

        if (!$this->dataCache->has(self::CACHE_KEY)) {
            return null;
        }

        $data = $this->dataCache->get(self::CACHE_KEY);

        if (!$data instanceof stdClass) {
            return null;
        }

        
        $timestamp = $data->timestamp;

        if (!$timestamp) {
            return null;
        }

        $period = '-' . $this->configDataProvider->getJwksCachePeriod();

        if ($timestamp < DateTime::createNow()->modify($period)->toTimestamp()) {
            return null;
        }

        
        $keys = $data->keys ?? null;

        if ($keys === null) {
            return null;
        }

        return $keys;
    }

    
    private function storeRawToCache(array $raw): void
    {
        if (!$this->config->get('useCache')) {
            return;
        }

        $data = (object) [
            'timestamp' => time(),
            'keys' => $raw,
        ];

        $this->dataCache->store(self::CACHE_KEY, $data);
    }
}
