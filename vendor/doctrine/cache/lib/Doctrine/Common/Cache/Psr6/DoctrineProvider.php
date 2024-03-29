<?php



namespace Doctrine\Common\Cache\Psr6;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\DoctrineAdapter as SymfonyDoctrineAdapter;

use function rawurlencode;


final class DoctrineProvider extends CacheProvider
{
    
    private $pool;

    public static function wrap(CacheItemPoolInterface $pool): Cache
    {
        if ($pool instanceof CacheAdapter) {
            return $pool->getCache();
        }

        if ($pool instanceof SymfonyDoctrineAdapter) {
            $getCache = function () {
                
                return $this->provider;
            };

            return $getCache->bindTo($pool, SymfonyDoctrineAdapter::class)();
        }

        return new self($pool);
    }

    private function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    
    public function getPool(): CacheItemPoolInterface
    {
        return $this->pool;
    }

    
    protected function doFetch($id)
    {
        $item = $this->pool->getItem(rawurlencode($id));

        return $item->isHit() ? $item->get() : false;
    }

    
    protected function doContains($id)
    {
        return $this->pool->hasItem(rawurlencode($id));
    }

    
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $item = $this->pool->getItem(rawurlencode($id));

        if (0 < $lifeTime) {
            $item->expiresAfter($lifeTime);
        }

        return $this->pool->save($item->set($data));
    }

    
    protected function doDelete($id)
    {
        return $this->pool->deleteItem(rawurlencode($id));
    }

    
    protected function doFlush()
    {
        return $this->pool->clear();
    }

    
    protected function doGetStats()
    {
        return null;
    }
}
