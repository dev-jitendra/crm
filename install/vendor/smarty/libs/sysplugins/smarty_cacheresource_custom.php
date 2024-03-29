<?php



abstract class Smarty_CacheResource_Custom extends Smarty_CacheResource
{
    
    abstract protected function fetch($id, $name, $cache_id, $compile_id, &$content, &$mtime);

    
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        return null;
    }

    
    abstract protected function save($id, $name, $cache_id, $compile_id, $exp_time, $content);

    
    abstract protected function delete($name, $cache_id, $compile_id, $exp_time);

    
    public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template)
    {
        $_cache_id = isset($cached->cache_id) ? preg_replace('![^\w\|]+!', '_', $cached->cache_id) : null;
        $_compile_id = isset($cached->compile_id) ? preg_replace('![^\w\|]+!', '_', $cached->compile_id) : null;

        $cached->filepath = sha1($cached->source->filepath . $_cache_id . $_compile_id);
        $this->populateTimestamp($cached);
    }

    
    public function populateTimestamp(Smarty_Template_Cached $cached)
    {
        $mtime = $this->fetchTimestamp($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id);
        if ($mtime !== null) {
            $cached->timestamp = $mtime;
            $cached->exists = !!$cached->timestamp;

            return;
        }
        $timestamp = null;
        $this->fetch($cached->filepath, $cached->source->name, $cached->cache_id, $cached->compile_id, $cached->content, $timestamp);
        $cached->timestamp = isset($timestamp) ? $timestamp : false;
        $cached->exists = !!$cached->timestamp;
    }

    
    public function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached=null)
    {
        if (!$cached) {
            $cached = $_template->cached;
        }
        $content = $cached->content ? $cached->content : null;
        $timestamp = $cached->timestamp ? $cached->timestamp : null;
        if ($content === null || !$timestamp) {
            $this->fetch(
                $_template->cached->filepath,
                $_template->source->name,
                $_template->cache_id,
                $_template->compile_id,
                $content,
                $timestamp
            );
        }
        if (isset($content)) {
            $_smarty_tpl = $_template;
            eval("?>" . $content);

            return true;
        }

        return false;
    }

    
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        return $this->save(
            $_template->cached->filepath,
            $_template->source->name,
            $_template->cache_id,
            $_template->compile_id,
            $_template->properties['cache_lifetime'],
            $content
        );
    }

    
    public function clearAll(Smarty $smarty, $exp_time=null)
    {
        $this->cache = array();

        return $this->delete(null, null, null, $exp_time);
    }

    
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time)
    {
        $this->cache = array();

        return $this->delete($resource_name, $cache_id, $compile_id, $exp_time);
    }

    
    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $id = $cached->filepath;
        $name = $cached->source->name . '.lock';

        $mtime = $this->fetchTimestamp($id, $name, null, null);
        if ($mtime === null) {
            $this->fetch($id, $name, null, null, $content, $mtime);
        }

        return $mtime && time() - $mtime < $smarty->locking_timeout;
    }

    
    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $cached->is_locked = true;

        $id = $cached->filepath;
        $name = $cached->source->name . '.lock';
        $this->save($id, $name, null, null, $smarty->locking_timeout, '');
    }

    
    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $cached->is_locked = false;

        $name = $cached->source->name . '.lock';
        $this->delete($name, null, null, null);
    }
}
