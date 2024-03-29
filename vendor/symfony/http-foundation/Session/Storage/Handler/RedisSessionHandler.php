<?php



namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Predis\Response\ErrorInterface;
use Symfony\Component\Cache\Traits\RedisClusterProxy;
use Symfony\Component\Cache\Traits\RedisProxy;


class RedisSessionHandler extends AbstractSessionHandler
{
    private $redis;

    
    private string $prefix;

    
    private ?int $ttl;

    
    public function __construct(\Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|RedisProxy|RedisClusterProxy $redis, array $options = [])
    {
        if ($diff = array_diff(array_keys($options), ['prefix', 'ttl'])) {
            throw new \InvalidArgumentException(sprintf('The following options are not supported "%s".', implode(', ', $diff)));
        }

        $this->redis = $redis;
        $this->prefix = $options['prefix'] ?? 'sf_s';
        $this->ttl = $options['ttl'] ?? null;
    }

    
    protected function doRead(string $sessionId): string
    {
        return $this->redis->get($this->prefix.$sessionId) ?: '';
    }

    
    protected function doWrite(string $sessionId, string $data): bool
    {
        $result = $this->redis->setEx($this->prefix.$sessionId, (int) ($this->ttl ?? \ini_get('session.gc_maxlifetime')), $data);

        return $result && !$result instanceof ErrorInterface;
    }

    
    protected function doDestroy(string $sessionId): bool
    {
        static $unlink = true;

        if ($unlink) {
            try {
                $unlink = false !== $this->redis->unlink($this->prefix.$sessionId);
            } catch (\Throwable $e) {
                $unlink = false;
            }
        }

        if (!$unlink) {
            $this->redis->del($this->prefix.$sessionId);
        }

        return true;
    }

    
    #[\ReturnTypeWillChange]
    public function close(): bool
    {
        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        return 0;
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return $this->redis->expire($this->prefix.$sessionId, (int) ($this->ttl ?? \ini_get('session.gc_maxlifetime')));
    }
}
