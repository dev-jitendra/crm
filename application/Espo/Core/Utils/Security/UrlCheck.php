<?php


namespace Espo\Core\Utils\Security;

use const DNS_A;
use const FILTER_FLAG_NO_PRIV_RANGE;
use const FILTER_FLAG_NO_RES_RANGE;
use const FILTER_VALIDATE_IP;
use const FILTER_VALIDATE_URL;
use const PHP_URL_HOST;

class UrlCheck
{
    public function isUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    
    public function isNotInternalUrl(string $url): bool
    {
        if (!$this->isUrl($url)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (!is_string($host)) {
            return false;
        }

        $records = dns_get_record($host, DNS_A);

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->ipAddressIsNotInternal($host);
        }

        if (!$records) {
            return false;
        }

        foreach ($records as $record) {
            
            $idAddress = $record['ip'] ?? null;

            if (!$idAddress) {
                return false;
            }

            if (!$this->ipAddressIsNotInternal($idAddress)) {
                return false;
            }
        }

        return true;
    }

    private function ipAddressIsNotInternal(string $ipAddress): bool
    {
        return (bool) filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
