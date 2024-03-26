<?php

namespace AsyncAws\Core\EndpointDiscovery;

use AsyncAws\Core\Exception\LogicException;


class EndpointCache
{
    
    private $endpoints = [];

    
    private $expired = [];

    
    public function addEndpoints(?string $region, array $endpoints): void
    {
        $now = time();

        if (null === $region) {
            $region = '';
        }
        if (!isset($this->endpoints[$region])) {
            $this->endpoints[$region] = [];
        }

        foreach ($endpoints as $endpoint) {
            $this->endpoints[$region][$this->sanitizeEndpoint($endpoint->getAddress())] = $now + ($endpoint->getCachePeriodInMinutes() * 60);
        }
        arsort($this->endpoints[$region]);
    }

    public function removeEndpoint(string $endpoint): void
    {
        $endpoint = $this->sanitizeEndpoint($endpoint);
        foreach ($this->endpoints as &$endpoints) {
            unset($endpoints[$endpoint]);
        }
        unset($endpoints);
        foreach ($this->expired as &$endpoints) {
            unset($endpoints[$endpoint]);
        }

        unset($endpoints);
    }

    public function getActiveEndpoint(?string $region): ?string
    {
        if (null === $region) {
            $region = '';
        }
        $now = time();

        foreach ($this->endpoints[$region] ?? [] as $endpoint => $expiresAt) {
            if ($expiresAt < $now) {
                $this->expired[$region] = \array_slice($this->expired[$region] ?? [], -100); 
                unset($this->endpoints[$region][$endpoint]);
                $this->expired[$region][$endpoint] = $expiresAt;

                continue;
            }

            return $endpoint;
        }

        return null;
    }

    public function getExpiredEndpoint(?string $region): ?string
    {
        if (null === $region) {
            $region = '';
        }
        if (empty($this->expired[$region])) {
            return null;
        }

        return array_key_last($this->expired[$region]);
    }

    private function sanitizeEndpoint(string $address): string
    {
        $parsed = parse_url($address);

        
        if (isset($parsed['host'])) {
            return rtrim(sprintf(
                '%s:
                $parsed['scheme'] ?? 'https',
                $parsed['host'],
                ltrim($parsed['path'] ?? '/', '/')
            ), '/');
        }

        
        if (isset($parsed['path'])) {
            $split = explode('/', $parsed['path'], 2);
            $parsed['host'] = $split[0];
            if (isset($split[1])) {
                $parsed['path'] = $split[1];
            } else {
                $parsed['path'] = '';
            }

            return rtrim(sprintf(
                '%s:
                $parsed['scheme'] ?? 'https',
                $parsed['host'],
                ltrim($parsed['path'], '/')
            ), '/');
        }

        throw new LogicException(sprintf('The supplied endpoint "%s" is invalid.', $address));
    }
}
