<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;
use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;


final class PsrCacheProvider implements CredentialProvider
{
    
    private $cache;

    
    private $decorated;

    
    private $logger;

    public function __construct(CredentialProvider $decorated, CacheItemPoolInterface $cache, ?LoggerInterface $logger = null)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function getCredentials(Configuration $configuration): ?Credentials
    {
        try {
            return $this->getFromCache($configuration);
        } catch (CacheException $e) {
            if (null !== $this->logger) {
                $this->logger->error('Failed to get AWS credentials from cache.', ['exception' => $e]);
            }

            return $this->decorated->getCredentials($configuration);
        }
    }

    
    private function getFromCache(Configuration $configuration): ?Credentials
    {
        $item = $this->cache->getItem('AsyncAws.Credentials.' . sha1(serialize([$configuration, \get_class($this->decorated)])));
        if (!$item->isHit()) {
            $item->set($credential = $this->decorated->getCredentials($configuration));

            if (null !== $credential && null !== $exp = $credential->getExpireDate()) {
                $item->expiresAt($exp);
                $this->cache->save($item);
            }
        }

        return $item->get();
    }
}
