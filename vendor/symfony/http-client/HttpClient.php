<?php



namespace Symfony\Component\HttpClient;

use Amp\Http\Client\Connection\ConnectionLimitingPool;
use Symfony\Contracts\HttpClient\HttpClientInterface;


final class HttpClient
{
    
    public static function create(array $defaultOptions = [], int $maxHostConnections = 6, int $maxPendingPushes = 50): HttpClientInterface
    {
        if ($amp = class_exists(ConnectionLimitingPool::class)) {
            if (!\extension_loaded('curl')) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }

            
            if (\PHP_VERSION_ID < 70217 || (\PHP_VERSION_ID >= 70300 && \PHP_VERSION_ID < 70304) || !\defined('CURLMOPT_PUSHFUNCTION')) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }

            static $curlVersion = null;
            $curlVersion = $curlVersion ?? curl_version();

            
            if (0x073d00 > $curlVersion['version_number'] || !(\CURL_VERSION_HTTP2 & $curlVersion['features'])) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }
        }

        if (\extension_loaded('curl')) {
            if ('\\' !== \DIRECTORY_SEPARATOR || isset($defaultOptions['cafile']) || isset($defaultOptions['capath']) || ini_get('curl.cainfo') || ini_get('openssl.cafile') || ini_get('openssl.capath')) {
                return new CurlHttpClient($defaultOptions, $maxHostConnections, $maxPendingPushes);
            }

            @trigger_error('Configure the "curl.cainfo", "openssl.cafile" or "openssl.capath" php.ini setting to enable the CurlHttpClient', \E_USER_WARNING);
        }

        if ($amp) {
            return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
        }

        @trigger_error((\extension_loaded('curl') ? 'Upgrade' : 'Install').' the curl extension or run "composer require amphp/http-client" to perform async HTTP operations, including full HTTP/2 support', \E_USER_NOTICE);

        return new NativeHttpClient($defaultOptions, $maxHostConnections);
    }

    
    public static function createForBaseUri(string $baseUri, array $defaultOptions = [], int $maxHostConnections = 6, int $maxPendingPushes = 50): HttpClientInterface
    {
        $client = self::create([], $maxHostConnections, $maxPendingPushes);

        return ScopingHttpClient::forBaseUri($client, $baseUri, $defaultOptions);
    }
}
