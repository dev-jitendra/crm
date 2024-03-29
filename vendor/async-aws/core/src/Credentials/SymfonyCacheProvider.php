<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;
use Psr\Cache\CacheException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


final class SymfonyCacheProvider implements CredentialProvider
{
    
    private $cache;

    
    private $decorated;

    
    private $logger;

    public function __construct(CredentialProvider $decorated, CacheInterface $cache, ?LoggerInterface $logger = null)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function getCredentials(Configuration $configuration): ?Credentials
    {
        $provider = $this->decorated;
        $closure = \Closure::fromCallable(static function (ItemInterface $item) use ($configuration, $provider) {
            $credential = $provider->getCredentials($configuration);

            if (null !== $credential && null !== $exp = $credential->getExpireDate()) {
                $item->expiresAt($exp);
            } else {
                $item->expiresAfter(0);
            }

            return $credential;
        });

        try {
            return $this->cache->get('AsyncAws.Credentials.' . sha1(serialize([$configuration, \get_class($this->decorated)])), $closure);
        } catch (CacheException $e) {
            if (null !== $this->logger) {
                $this->logger->error('Failed to get AWS credentials from cache.', ['exception' => $e]);
            }

            return $provider->getCredentials($configuration);
        }
    }
}
