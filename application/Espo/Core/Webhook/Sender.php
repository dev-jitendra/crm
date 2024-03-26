<?php


namespace Espo\Core\Webhook;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Json;
use Espo\Entities\Webhook;


class Sender
{
    private const CONNECT_TIMEOUT = 5;
    private const TIMEOUT = 10;

    public function __construct(private Config $config)
    {}

    
    public function send(Webhook $webhook, array $dataList): int
    {
        $payload = Json::encode($dataList);

        $signature = null;

        $secretKey = $webhook->getSecretKey();

        if ($secretKey) {
            $signature = $this->buildSignature($webhook, $payload, $secretKey);
        }

        $connectTimeout = $this->config->get('webhookConnectTimeout', self::CONNECT_TIMEOUT);
        $timeout = $this->config->get('webhookTimeout', self::TIMEOUT);

        $headerList = [];

        $headerList[] = 'Content-Type: application/json';
        $headerList[] = 'Content-Length: ' . strlen($payload);

        if ($signature) {
            $headerList[] = 'X-Signature: ' . $signature;
        }

        $url = $webhook->getUrl();

        if (!$url) {
            throw new Error("Webhook does not have URL.");
        }

        $handler = curl_init($url);

        if ($handler === false) {
            throw new Error("Could not init CURL for URL {$url}.");
        }

        curl_setopt($handler, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, \CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handler, \CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($handler, \CURLOPT_HEADER, true);
        curl_setopt($handler, \CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handler, \CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($handler, \CURLOPT_TIMEOUT, $timeout);
        curl_setopt($handler, \CURLOPT_PROTOCOLS, \CURLPROTO_HTTPS | \CURLPROTO_HTTP);
        curl_setopt($handler, \CURLOPT_REDIR_PROTOCOLS, \CURLPROTO_HTTPS);
        curl_setopt($handler, \CURLOPT_HTTPHEADER, $headerList);
        curl_setopt($handler, \CURLOPT_POSTFIELDS, $payload);

        curl_exec($handler);

        $code = curl_getinfo($handler, \CURLINFO_HTTP_CODE);

        if (!is_numeric($code)) {
            $code = 0;
        }

        if (!is_int($code)) {
            $code = intval($code);
        }

        $errorNumber = curl_errno($handler);

        if (
            $errorNumber &&
            in_array($errorNumber, [\CURLE_OPERATION_TIMEDOUT, \CURLE_OPERATION_TIMEOUTED])
        ) {
            $code = 408;
        }

        curl_close($handler);

        return $code;
    }

    private function buildSignature(Webhook $webhook, string $payload, string $secretKey): string
    {
        return base64_encode($webhook->getId() . ':' . hash_hmac('sha256', $payload, $secretKey, true));
    }
}
