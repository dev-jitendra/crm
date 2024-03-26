<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;


class MemcachedSessionHandler extends AbstractSessionHandler
{
    private $memcached;

    
    private ?int $ttl;

    
    private string $prefix;

    
    public function __construct(\Memcached $memcached, array $options = [])
    {
        $this->memcached = $memcached;

        if ($diff = array_diff(array_keys($options), ['prefix', 'expiretime', 'ttl'])) {
            throw new \InvalidArgumentException(sprintf('The following options are not supported "%s".', implode(', ', $diff)));
        }

        $this->ttl = $options['expiretime'] ?? $options['ttl'] ?? null;
        $this->prefix = $options['prefix'] ?? 'sf2s';
    }

    public function close(): bool
    {
        return $this->memcached->quit();
    }

    
    protected function doRead(string $sessionId): string
    {
        return $this->memcached->get($this->prefix.$sessionId) ?: '';
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        $this->memcached->touch($this->prefix.$sessionId, $this->getCompatibleTtl());

        return true;
    }

    
    protected function doWrite(string $sessionId, string $data): bool
    {
        return $this->memcached->set($this->prefix.$sessionId, $data, $this->getCompatibleTtl());
    }

    private function getCompatibleTtl(): int
    {
        $ttl = (int) ($this->ttl ?? \ini_get('session.gc_maxlifetime'));

        
        
        if ($ttl > 60 * 60 * 24 * 30) {
            $ttl += time();
        }

        return $ttl;
    }

    
    protected function doDestroy(string $sessionId): bool
    {
        $result = $this->memcached->delete($this->prefix.$sessionId);

        return $result || \Memcached::RES_NOTFOUND == $this->memcached->getResultCode();
    }

    public function gc(int $maxlifetime): int|false
    {
        
        return 0;
    }

    
    protected function getMemcached(): \Memcached
    {
        return $this->memcached;
    }
}
