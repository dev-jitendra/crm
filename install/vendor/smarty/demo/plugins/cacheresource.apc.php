<?php


class Smarty_CacheResource_Apc extends Smarty_CacheResource_KeyValueStore
{
    public function __construct()
    {
        
        if (!function_exists('apc_cache_info')) {
            throw new Exception('APC Template Caching Error: APC is not installed');
        }
    }

    
    protected function read(array $keys)
    {
        $_res = array();
        $res = apc_fetch($keys);
        foreach ($res as $k => $v) {
            $_res[$k] = $v;
        }

        return $_res;
    }

    
    protected function write(array $keys, $expire=null)
    {
        foreach ($keys as $k => $v) {
            apc_store($k, $v, $expire);
        }

        return true;
    }

    
    protected function delete(array $keys)
    {
        foreach ($keys as $k) {
            apc_delete($k);
        }

        return true;
    }

    
    protected function purge()
    {
        return apc_clear_cache('user');
    }
}
