<?php


namespace Espo\Tools\AdminNotifications;

class LatestReleaseDataRequester
{
    
    public function request(
        ?string $url = null,
        array $requestData = [],
        string $urlPath = 'release/latest'
    ): ?array {

        if (!function_exists('curl_version')) {
            return null;
        }

        $ch = curl_init();

        $requestUrl = $url ? trim($url) : base64_decode('aHR0cHM6Ly9zLmVzcG9jcm0uY29tLw==');
        $requestUrl = (substr($requestUrl, -1) == '/') ? $requestUrl : $requestUrl . '/';

        $requestUrl .= empty($requestData) ?
            $urlPath . '/' :
            $urlPath . '/?' . http_build_query($requestData);

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);

        
        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($result === false) {
            return null;
        }

        if ($httpCode !== 200) {
            return null;
        }

        $data = json_decode($result, true);

        if (!is_array($data)) {
            return null;
        }

        return $data;
    }
}
