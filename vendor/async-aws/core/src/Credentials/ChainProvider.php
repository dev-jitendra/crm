<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ResetInterface;


final class ChainProvider implements CredentialProvider, ResetInterface
{
    
    private $providers;

    
    private $lastSuccessfulProvider = [];

    
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function getCredentials(Configuration $configuration): ?Credentials
    {
        $key = spl_object_hash($configuration);
        if (\array_key_exists($key, $this->lastSuccessfulProvider)) {
            if (null === $provider = $this->lastSuccessfulProvider[$key]) {
                return null;
            }

            return $provider->getCredentials($configuration);
        }

        foreach ($this->providers as $provider) {
            if (null !== $credentials = $provider->getCredentials($configuration)) {
                $this->lastSuccessfulProvider[$key] = $provider;

                return $credentials;
            }
        }

        $this->lastSuccessfulProvider[$key] = null;

        return null;
    }

    public function reset(): void
    {
        $this->lastSuccessfulProvider = [];
    }

    public static function createDefaultChain(?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null): CredentialProvider
    {
        $httpClient = $httpClient ?? HttpClient::create();
        $logger = $logger ?? new NullLogger();

        return new ChainProvider([
            new ConfigurationProvider(),
            new WebIdentityProvider($logger, null, $httpClient),
            new IniFileProvider($logger, null, $httpClient),
            new ContainerProvider($httpClient, $logger),
            new InstanceProvider($httpClient, $logger),
        ]);
    }
}
