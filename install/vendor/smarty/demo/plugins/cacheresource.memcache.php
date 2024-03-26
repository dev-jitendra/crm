<?php


class Smarty_CacheResource_Memcache extends Smarty_CacheResource_KeyValueStore
{
    
    protected $memcache = null;

    public function __construct()
    {
        $this->memcache = new Memcache();
        $this->memcache->addServer( '127.0.0.1', 11211 );
    }

    
    protected function read(array $keys)
    {
        $_keys = $lookup = array();
        foreach ($keys as $k) {
            $_k = sha1($k);
            $_keys[] = $_k;
            $lookup[$_k] = $k;
        }
        $_res = array();
        $res = $this->memcache->get($_keys);
        foreach ($res as $k => $v) {
            $_res[$lookup[$k]] = $v;
        }

        return $_res;
    }

    
    protected function write(array $keys, $expire=null)
    {
        foreach ($keys as $k => $v) {
            $k = sha1($k);
            $this->memcache->set($k, $v, 0, $expire);
        }

        return true;
    }

    
    protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            $k = sha1($k);
            $this->memcache->delete($k);
        }

        return true;
    }

    
    protected function purge()
    {
        return $this->memcache->flush();
    }
}
