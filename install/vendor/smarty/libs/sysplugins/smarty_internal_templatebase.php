<?php



abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data
{
    
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($template === null && $this instanceof $this->template_class) {
            $template = $this;
        }
        if (!empty($cache_id) && is_object($cache_id)) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if ($parent === null && ($this instanceof Smarty || is_string($template))) {
            $parent = $this;
        }
        
        $_template = ($template instanceof $this->template_class)
        ? $template
        : $this->smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
        
       
        
        if ($this instanceof Smarty) {
            $_template->caching = $this->caching;
        }
        
        if ($merge_tpl_vars) {
            
            $save_tpl_vars = $_template->tpl_vars;
            $save_config_vars = $_template->config_vars;
            $ptr_array = array($_template);
            $ptr = $_template;
            while (isset($ptr->parent)) {
                $ptr_array[] = $ptr = $ptr->parent;
            }
            $ptr_array = array_reverse($ptr_array);
            $parent_ptr = reset($ptr_array);
            $tpl_vars = $parent_ptr->tpl_vars;
            $config_vars = $parent_ptr->config_vars;
            while ($parent_ptr = next($ptr_array)) {
                if (!empty($parent_ptr->tpl_vars)) {
                    $tpl_vars = array_merge($tpl_vars, $parent_ptr->tpl_vars);
                }
                if (!empty($parent_ptr->config_vars)) {
                    $config_vars = array_merge($config_vars, $parent_ptr->config_vars);
                }
            }
            if (!empty(Smarty::$global_tpl_vars)) {
                $tpl_vars = array_merge(Smarty::$global_tpl_vars, $tpl_vars);
            }
            $_template->tpl_vars = $tpl_vars;
            $_template->config_vars = $config_vars;
        }
        
        if (!isset($_template->tpl_vars['smarty'])) {
            $_template->tpl_vars['smarty'] = new Smarty_Variable;
        }
        if (isset($this->smarty->error_reporting)) {
            $_smarty_old_error_level = error_reporting($this->smarty->error_reporting);
        }
        
        if (!$this->smarty->debugging && $this->smarty->debugging_ctrl == 'URL') {
            if (isset($_SERVER['QUERY_STRING'])) {
                $_query_string = $_SERVER['QUERY_STRING'];
            } else {
                $_query_string = '';
            }
            if (false !== strpos($_query_string, $this->smarty->smarty_debug_id)) {
                if (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=on')) {
                    
                    setcookie('SMARTY_DEBUG', true);
                    $this->smarty->debugging = true;
                } elseif (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=off')) {
                    
                    setcookie('SMARTY_DEBUG', false);
                    $this->smarty->debugging = false;
                } else {
                    
                    $this->smarty->debugging = true;
                }
            } else {
                if (isset($_COOKIE['SMARTY_DEBUG'])) {
                    $this->smarty->debugging = true;
                }
            }
        }
        
        $_template->smarty->merged_templates_func = array();
        
        
        if ($_template->source->recompiled) {
            $_template->caching = false;
        }
        
        if (!$_template->source->exists) {
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $parent_resource = " in '{$_template->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$_template->source->type} '{$_template->source->name}'{$parent_resource}");
        }
        
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || !$_template->cached->valid) {
            
            if (!$_template->source->uncompiled) {
                $_smarty_tpl = $_template;
                if ($_template->source->recompiled) {
                    $code = $_template->compiler->compileTemplate($_template);
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        eval("?>" . $code);
                        unset($code);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    if (!$_template->compiled->exists || ($_template->smarty->force_compile && !$_template->compiled->isCompiled)) {
                        $_template->compileTemplateSource();
                        $code = file_get_contents($_template->compiled->filepath);
                        eval("?>" . $code);
                        unset($code);
                        $_template->compiled->loaded = true;
                        $_template->compiled->isCompiled = true;
                    }
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    if (!$_template->compiled->loaded) {
                        include($_template->compiled->filepath);
                        if ($_template->mustCompile) {
                            
                            $_template->compileTemplateSource();
                            $code = file_get_contents($_template->compiled->filepath);
                            eval("?>" . $code);
                            unset($code);
                            $_template->compiled->isCompiled = true;
                        }
                        $_template->compiled->loaded = true;
                    } else {
                        $_template->decodeProperties($_template->compiled->_properties, false);
                    }
                    try {
                        ob_start();
                        if (empty($_template->properties['unifunc']) || !is_callable($_template->properties['unifunc'])) {
                            throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
                        }
                        array_unshift($_template->_capture_stack,array());
                        
                        
                        
                        $_template->properties['unifunc']($_template);
                        
                        if (isset($_template->_capture_stack[0][0])) {
                            $_template->capture_error();
                        }
                        array_shift($_template->_capture_stack);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                }
            } else {
                if ($_template->source->uncompiled) {
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        $_template->source->renderUncompiled($_template);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    throw new SmartyException("Resource '$_template->source->type' must have 'renderUncompiled' method");
                }
            }
            $_output = ob_get_clean();
            if (!$_template->source->recompiled && empty($_template->properties['file_dependency'][$_template->source->uid])) {
                $_template->properties['file_dependency'][$_template->source->uid] = array($_template->source->filepath, $_template->source->timestamp, $_template->source->type);
            }
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $_template->parent->properties['file_dependency'] = array_merge($_template->parent->properties['file_dependency'], $_template->properties['file_dependency']);
                foreach ($_template->required_plugins as $code => $tmp1) {
                    foreach ($tmp1 as $name => $tmp) {
                        foreach ($tmp as $type => $data) {
                            $_template->parent->required_plugins[$code][$name][$type] = $data;
                        }
                    }
                }
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_render($_template);
            }
            
            if (!$_template->source->recompiled && ($_template->caching == Smarty::CACHING_LIFETIME_SAVED || $_template->caching == Smarty::CACHING_LIFETIME_CURRENT)) {
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::start_cache($_template);
                }
                $_template->properties['has_nocache_code'] = false;
                
                $cache_split = preg_split("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output);
                
                preg_match_all("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output, $cache_parts);
                $output = '';
                
                foreach ($cache_split as $curr_idx => $curr_split) {
                    
                    $output .= preg_replace('/(<%|%>|<\?php|<\?|\?>)/', "<?php echo '\$1'; ?>\n", $curr_split);
                    if (isset($cache_parts[0][$curr_idx])) {
                        $_template->properties['has_nocache_code'] = true;
                        
                        $output .= preg_replace("!/\*/?%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!", '', $cache_parts[0][$curr_idx]);
                    }
                }
                if (!$no_output_filter && !$_template->has_nocache_code && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
                    $output = Smarty_Internal_Filter_Handler::runFilter('output', $output, $_template);
                }
                
                $_smarty_tpl = $_template;
                try {
                    ob_start();
                    eval("?>" . $output);
                    $_output = ob_get_clean();
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
                
                $_template->writeCachedContent($output);
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::end_cache($_template);
                }
            } else {
                
                if (!empty($_template->properties['nocache_hash']) && !empty($_template->parent->properties['nocache_hash'])) {
                    
                    $_output = str_replace("{$_template->properties['nocache_hash']}", $_template->parent->properties['nocache_hash'], $_output);
                    $_template->parent->has_nocache_code = $_template->parent->has_nocache_code || $_template->has_nocache_code;
                }
            }
        } else {
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::start_cache($_template);
            }
            try {
                ob_start();
                array_unshift($_template->_capture_stack,array());
                
                
                
                $_template->properties['unifunc']($_template);
                
                if (isset($_template->_capture_stack[0][0])) {
                    $_template->capture_error();
                }
                array_shift($_template->_capture_stack);
                $_output = ob_get_clean();
            } catch (Exception $e) {
                ob_get_clean();
                throw $e;
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_cache($_template);
            }
        }
        if ((!$this->caching || $_template->has_nocache_code || $_template->source->recompiled) && !$no_output_filter && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
            $_output = Smarty_Internal_Filter_Handler::runFilter('output', $_output, $_template);
        }
        if (isset($this->error_reporting)) {
            error_reporting($_smarty_old_error_level);
        }
        
        if ($display) {
            if ($this->caching && $this->cache_modified_check) {
                $_isCached = $_template->isCached() && !$_template->has_nocache_code;
                $_last_modified_date = @substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
                if ($_isCached && $_template->cached->timestamp <= strtotime($_last_modified_date)) {
                    switch (PHP_SAPI) {
                        case 'cgi':         
                        case 'cgi-fcgi':    
                        case 'fpm-fcgi':    
                        header('Status: 304 Not Modified');
                        break;

                        case 'cli':
                        if (!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])) {
                            $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                        }
                        break;

                        default:
                        header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                        break;
                    }
                } else {
                    switch (PHP_SAPI) {
                        case 'cli':
                        if (!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])) {
                            $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $_template->cached->timestamp) . ' GMT';
                        }
                        break;

                        default:
                        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $_template->cached->timestamp) . ' GMT');
                        break;
                    }
                    echo $_output;
                }
            } else {
                echo $_output;
            }
            
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::display_debug($this);
            }
            if ($merge_tpl_vars) {
                
                $_template->tpl_vars = $save_tpl_vars;
                $_template->config_vars =  $save_config_vars;
            }

            return;
        } else {
            if ($merge_tpl_vars) {
                
                $_template->tpl_vars = $save_tpl_vars;
                $_template->config_vars =  $save_config_vars;
            }
            
            return $_output;
        }
    }

    
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        
        $this->fetch($template, $cache_id, $compile_id, $parent, true);
    }

    
    public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        if ($template === null && $this instanceof $this->template_class) {
            return $this->cached->valid;
        }
        if (!($template instanceof $this->template_class)) {
            if ($parent === null) {
                $parent = $this;
            }
            $template = $this->smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
        }
        
        return $template->cached->valid;
    }

    
    public function createData($parent = null)
    {
        return new Smarty_Data($parent, $this);
    }

    
    public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
    {
        if (isset($this->smarty->registered_plugins[$type][$tag])) {
            throw new SmartyException("Plugin tag \"{$tag}\" already registered");
        } elseif (!is_callable($callback)) {
            throw new SmartyException("Plugin \"{$tag}\" not callable");
        } else {
            $this->smarty->registered_plugins[$type][$tag] = array($callback, (bool) $cacheable, (array) $cache_attr);
        }

        return $this;
    }

    
    public function unregisterPlugin($type, $tag)
    {
        if (isset($this->smarty->registered_plugins[$type][$tag])) {
            unset($this->smarty->registered_plugins[$type][$tag]);
        }

        return $this;
    }

    
    public function registerResource($type, $callback)
    {
        $this->smarty->registered_resources[$type] = $callback instanceof Smarty_Resource ? $callback : array($callback, false);

        return $this;
    }

    
    public function unregisterResource($type)
    {
        if (isset($this->smarty->registered_resources[$type])) {
            unset($this->smarty->registered_resources[$type]);
        }

        return $this;
    }

    
    public function registerCacheResource($type, Smarty_CacheResource $callback)
    {
        $this->smarty->registered_cache_resources[$type] = $callback;

        return $this;
    }

    
    public function unregisterCacheResource($type)
    {
        if (isset($this->smarty->registered_cache_resources[$type])) {
            unset($this->smarty->registered_cache_resources[$type]);
        }

        return $this;
    }

    
    public function registerObject($object_name, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array())
    {
        
        if (!empty($allowed)) {
            foreach ((array) $allowed as $method) {
                if (!is_callable(array($object_impl, $method)) && !property_exists($object_impl, $method)) {
                    throw new SmartyException("Undefined method or property '$method' in registered object");
                }
            }
        }
        
        if (!empty($block_methods)) {
            foreach ((array) $block_methods as $method) {
                if (!is_callable(array($object_impl, $method))) {
                    throw new SmartyException("Undefined method '$method' in registered object");
                }
            }
        }
        
        $this->smarty->registered_objects[$object_name] =
        array($object_impl, (array) $allowed, (boolean) $smarty_args, (array) $block_methods);

        return $this;
    }

    
    public function getRegisteredObject($name)
    {
        if (!isset($this->smarty->registered_objects[$name])) {
            throw new SmartyException("'$name' is not a registered object");
        }
        if (!is_object($this->smarty->registered_objects[$name][0])) {
            throw new SmartyException("registered '$name' is not an object");
        }

        return $this->smarty->registered_objects[$name][0];
    }

    
    public function unregisterObject($name)
    {
        if (isset($this->smarty->registered_objects[$name])) {
            unset($this->smarty->registered_objects[$name]);
        }

        return $this;
    }

    
    public function registerClass($class_name, $class_impl)
    {
        
        if (!class_exists($class_impl)) {
            throw new SmartyException("Undefined class '$class_impl' in register template class");
        }
        
        $this->smarty->registered_classes[$class_name] = $class_impl;

        return $this;
    }

    
    public function registerDefaultPluginHandler($callback)
    {
        if (is_callable($callback)) {
            $this->smarty->default_plugin_handler_func = $callback;
        } else {
            throw new SmartyException("Default plugin handler '$callback' not callable");
        }

        return $this;
    }

    
    public function registerDefaultTemplateHandler($callback)
    {
        if (is_callable($callback)) {
            $this->smarty->default_template_handler_func = $callback;
        } else {
            throw new SmartyException("Default template handler '$callback' not callable");
        }

        return $this;
    }

    
    public function registerDefaultConfigHandler($callback)
    {
        if (is_callable($callback)) {
            $this->smarty->default_config_handler_func = $callback;
        } else {
            throw new SmartyException("Default config handler '$callback' not callable");
        }

        return $this;
    }

    
    public function registerFilter($type, $callback)
    {
        $this->smarty->registered_filters[$type][$this->_get_filter_name($callback)] = $callback;

        return $this;
    }

    
    public function unregisterFilter($type, $callback)
    {
        $name = $this->_get_filter_name($callback);
        if (isset($this->smarty->registered_filters[$type][$name])) {
            unset($this->smarty->registered_filters[$type][$name]);
        }

        return $this;
    }

    
    public function _get_filter_name($function_name)
    {
        if (is_array($function_name)) {
            $_class_name = (is_object($function_name[0]) ?
            get_class($function_name[0]) : $function_name[0]);

            return $_class_name . '_' . $function_name[1];
        } else {
            return $function_name;
        }
    }

    
    public function loadFilter($type, $name)
    {
        $_plugin = "smarty_{$type}filter_{$name}";
        $_filter_name = $_plugin;
        if ($this->smarty->loadPlugin($_plugin)) {
            if (class_exists($_plugin, false)) {
                $_plugin = array($_plugin, 'execute');
            }
            if (is_callable($_plugin)) {
                $this->smarty->registered_filters[$type][$_filter_name] = $_plugin;

                return true;
            }
        }
        throw new SmartyException("{$type}filter \"{$name}\" not callable");
    }

    
    public function unloadFilter($type, $name)
    {
        $_filter_name = "smarty_{$type}filter_{$name}";
        if (isset($this->smarty->registered_filters[$type][$_filter_name])) {
            unset ($this->smarty->registered_filters[$type][$_filter_name]);
        }

        return $this;
    }

    
    private function replaceCamelcase($match)
    {
        return "_" . strtolower($match[1]);
    }

    
    public function __call($name, $args)
    {
        static $_prefixes = array('set' => true, 'get' => true);
        static $_resolved_property_name = array();
        static $_resolved_property_source = array();

        
        if (method_exists($this->smarty, $name)) {
            return call_user_func_array(array($this->smarty, $name), $args);
        }
        
        $first3 = strtolower(substr($name, 0, 3));
        if (isset($_prefixes[$first3]) && isset($name[3]) && $name[3] !== '_') {
            if (isset($_resolved_property_name[$name])) {
                $property_name = $_resolved_property_name[$name];
            } else {
                
                
                $property_name = strtolower(substr($name, 3, 1)) . substr($name, 4);
                
                $property_name = preg_replace_callback('/([A-Z])/', array($this,'replaceCamelcase'), $property_name);
                $_resolved_property_name[$name] = $property_name;
            }
            if (isset($_resolved_property_source[$property_name])) {
                $_is_this = $_resolved_property_source[$property_name];
            } else {
                $_is_this = null;
                if (property_exists($this, $property_name)) {
                    $_is_this = true;
                } elseif (property_exists($this->smarty, $property_name)) {
                    $_is_this = false;
                }
                $_resolved_property_source[$property_name] = $_is_this;
            }
            if ($_is_this) {
                if ($first3 == 'get')
                return $this->$property_name;
                else
                return $this->$property_name = $args[0];
            } elseif ($_is_this === false) {
                if ($first3 == 'get')
                return $this->smarty->$property_name;
                else
                return $this->smarty->$property_name = $args[0];
            } else {
                throw new SmartyException("property '$property_name' does not exist.");

                return false;
            }
        }
        if ($name == 'Smarty') {
            throw new SmartyException("PHP5 requires you to call __construct() instead of Smarty()");
        }
        
        throw new SmartyException("Call of unknown method '$name'.");
    }

}
