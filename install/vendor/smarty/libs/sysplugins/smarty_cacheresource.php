<?php



abstract class Smarty_CacheResource
{
    
    public static $resources = array();

    
    protected static $sysplugins = array(
        'file' => true,
    );

    
    abstract public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template);

    
    abstract public function populateTimestamp(Smarty_Template_Cached $cached);

    
    abstract public function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached=null);

    
    abstract public function writeCachedContent(Smarty_Internal_Template $_template, $content);

    
    public function getCachedContent(Smarty_Internal_Template $_template)
    {
        if ($_template->cached->handler->process($_template)) {
            ob_start();
            $_template->properties['unifunc']($_template);

            return ob_get_clean();
        }

        return null;
    }

    
    abstract public function clearAll(Smarty $smarty, $exp_time=null);

    
    abstract public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time);

    public function locked(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        
        $start = microtime(true);
        $hadLock = null;
        while ($this->hasLock($smarty, $cached)) {
            $hadLock = true;
            if (microtime(true) - $start > $smarty->locking_timeout) {
                
                return false;
            }
            sleep(1);
        }

        return $hadLock;
    }

    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        
        return false;
    }

    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        
        return true;
    }

    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        
        return true;
    }

    
    public static function load(Smarty $smarty, $type = null)
    {
        if (!isset($type)) {
            $type = $smarty->caching_type;
        }

        
        if (isset($smarty->_cacheresource_handlers[$type])) {
            return $smarty->_cacheresource_handlers[$type];
        }

        
        if (isset($smarty->registered_cache_resources[$type])) {
            
            return $smarty->_cacheresource_handlers[$type] = $smarty->registered_cache_resources[$type];
        }
        
        if (isset(self::$sysplugins[$type])) {
            if (!isset(self::$resources[$type])) {
                $cache_resource_class = 'Smarty_Internal_CacheResource_' . ucfirst($type);
                self::$resources[$type] = new $cache_resource_class();
            }

            return $smarty->_cacheresource_handlers[$type] = self::$resources[$type];
        }
        
        $cache_resource_class = 'Smarty_CacheResource_' . ucfirst($type);
        if ($smarty->loadPlugin($cache_resource_class)) {
            if (!isset(self::$resources[$type])) {
                self::$resources[$type] = new $cache_resource_class();
            }

            return $smarty->_cacheresource_handlers[$type] = self::$resources[$type];
        }
        
        throw new SmartyException("Unable to load cache resource '{$type}'");
    }

    
    public static function invalidLoadedCache(Smarty $smarty)
    {
        foreach ($smarty->template_objects as $tpl) {
            if (isset($tpl->cached)) {
                $tpl->cached->valid = false;
                $tpl->cached->processed = false;
            }
        }
    }
}


class Smarty_Template_Cached
{
    
    public $filepath = false;

    
    public $content = null;

    
    public $timestamp = false;

    
    public $exists = false;

    
    public $valid = false;

    
    public $processed = false;

    
    public $handler = null;

    
    public $compile_id = null;

    
    public $cache_id = null;

    
    public $lock_id = null;

    
    public $is_locked = false;

    
    public $source = null;

    
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->compile_id = $_template->compile_id;
        $this->cache_id = $_template->cache_id;
        $this->source = $_template->source;
        $_template->cached = $this;
        $smarty = $_template->smarty;

        
        
        
        $this->handler = $handler = Smarty_CacheResource::load($smarty); 

        
        
        
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || $_template->source->recompiled) {
            $handler->populate($this, $_template);

            return;
        }
        while (true) {
            while (true) {
                $handler->populate($this, $_template);
                if ($this->timestamp === false || $smarty->force_compile || $smarty->force_cache) {
                    $this->valid = false;
                } else {
                    $this->valid = true;
                }
                if ($this->valid && $_template->caching == Smarty::CACHING_LIFETIME_CURRENT && $_template->cache_lifetime >= 0 && time() > ($this->timestamp + $_template->cache_lifetime)) {
                    
                    $this->valid = false;
                }
                if ($this->valid || !$_template->smarty->cache_locking) {
                    break;
                }
                if (!$this->handler->locked($_template->smarty, $this)) {
                    $this->handler->acquireLock($_template->smarty, $this);
                    break 2;
                }
            }
            if ($this->valid) {
                if (!$_template->smarty->cache_locking || $this->handler->locked($_template->smarty, $this) === null) {
                    
                    if ($smarty->debugging) {
                        Smarty_Internal_Debug::start_cache($_template);
                    }
                    if ($handler->process($_template, $this) === false) {
                        $this->valid = false;
                    } else {
                        $this->processed = true;
                    }
                    if ($smarty->debugging) {
                        Smarty_Internal_Debug::end_cache($_template);
                    }
                } else {
                    continue;
                }
            } else {
                return;
            }
            if ($this->valid && $_template->caching === Smarty::CACHING_LIFETIME_SAVED && $_template->properties['cache_lifetime'] >= 0 && (time() > ($_template->cached->timestamp + $_template->properties['cache_lifetime']))) {
                $this->valid = false;
            }
            if (!$this->valid && $_template->smarty->cache_locking) {
                $this->handler->acquireLock($_template->smarty, $this);

                return;
            } else {
                return;
            }
        }
    }

    
    public function write(Smarty_Internal_Template $_template, $content)
    {
        if (!$_template->source->recompiled) {
            if ($this->handler->writeCachedContent($_template, $content)) {
                $this->timestamp = time();
                $this->exists = true;
                $this->valid = true;
                if ($_template->smarty->cache_locking) {
                    $this->handler->releaseLock($_template->smarty, $this);
                }

                return true;
            }
        }

        return false;
    }

}
