<?php




abstract class Smarty_Internal_TemplateCompilerBase
{
    
    private $nocache_hash = null;

    
    public $suppressNocacheProcessing = false;

    
    public $suppressMergedTemplates = false;

    
    public static $_tag_objects = array();

    
    public $_tag_stack = array();

    
    public $template = null;

    
    public $merged_templates = array();

    
    public $sources = array();

    
    public $inheritance = false;

    
    public $inheritance_child = false;

    
    public $extends_uid = array();

    
    public $trace_line_offset = 0;

    
    public $trace_uid = '';

    
    public $trace_filepath = '';
    
    public $trace_stack = array();

    
    public $default_handler_plugins = array();

    
    public $default_modifier_list = null;

    
    public $forceNocache = false;

    
    public $suppressHeader = false;

    
    public $suppressTemplatePropertyHeader = false;

    
    public $suppressFilter = false;

    
    public $write_compiled_code = true;

    
    public $compiles_template_function = false;

    
    public $called_functions = array();

    
    public $modifier_plugins = array();

    
    public $known_modifier_type = array();

    
    abstract protected function doCompile($_content);

    
    public function __construct()
    {
        $this->nocache_hash = str_replace('.', '-', uniqid(rand(), true));
    }

    
    public function compileTemplate(Smarty_Internal_Template $template, $nocache = false)
    {
        if (empty($template->properties['nocache_hash'])) {
            $template->properties['nocache_hash'] = $this->nocache_hash;
        } else {
            $this->nocache_hash = $template->properties['nocache_hash'];
        }
        
        $this->nocache = $nocache;
        $this->tag_nocache = false;
        
        $this->template = $template;
        
        $this->template->has_nocache_code = false;
        $save_source = $this->template->source;
        
        $template_header = '';
        if (!$this->suppressHeader) {
            $template_header .= "<?php  ?>\n";
        }

        if (empty($this->template->source->components)) {
            $this->sources = array($template->source);
        } else {
            
            $this->sources = array_reverse($template->source->components);
        }
        $loop = 0;
        
        while ($this->template->source = array_shift($this->sources)) {
            $this->smarty->_current_file = $this->template->source->filepath;
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::start_compile($this->template);
            }
            $no_sources = count($this->sources);
            if ($loop || $no_sources) {
                $this->template->properties['file_dependency'][$this->template->source->uid] = array($this->template->source->filepath, $this->template->source->timestamp, $this->template->source->type);
            }
            $loop++;
            if ($no_sources) {
                $this->inheritance_child = true;
            } else {
                $this->inheritance_child = false;
            }
            do {
                $_compiled_code = '';
                
                $this->abort_and_recompile = false;
                
                $_content = $this->template->source->content;
                if ($_content != '') {
                    
                    if ((isset($this->smarty->autoload_filters['pre']) || isset($this->smarty->registered_filters['pre'])) && !$this->suppressFilter) {
                        $_content = Smarty_Internal_Filter_Handler::runFilter('pre', $_content, $template);
                    }
                    
                    $_compiled_code = $this->doCompile($_content);
                }
            } while ($this->abort_and_recompile);
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_compile($this->template);
            }
        }
        
        $this->template->source = $save_source;
        unset($save_source);
        $this->smarty->_current_file = $this->template->source->filepath;
        
        unset($this->parser->root_buffer, $this->parser->current_buffer, $this->parser, $this->lex, $this->template);
        self::$_tag_objects = array();
        
        $merged_code = '';
        if (!$this->suppressMergedTemplates && !empty($this->merged_templates)) {
            foreach ($this->merged_templates as $code) {
                $merged_code .= $code;
            }
        }
        
        if ((isset($this->smarty->autoload_filters['post']) || isset($this->smarty->registered_filters['post'])) && !$this->suppressFilter && $_compiled_code != '') {
            $_compiled_code = Smarty_Internal_Filter_Handler::runFilter('post', $_compiled_code, $template);
        }
        if ($this->suppressTemplatePropertyHeader) {
            $code = $_compiled_code . $merged_code;
        } else {
            $code = $template_header . $template->createTemplateCodeFrame($_compiled_code) . $merged_code;
        }
        
        unset ($template->source->content);

        return $code;
    }

    
    public function compileTag($tag, $args, $parameter = array())
    {
        
        
        $this->has_code = true;
        $this->has_output = false;
        
        if (isset($this->smarty->get_used_tags) && $this->smarty->get_used_tags) {
            $this->template->used_tags[] = array($tag, $args);
        }
        
        if (in_array("'nocache'", $args) || in_array(array('nocache' => 'true'), $args)
            || in_array(array('nocache' => '"true"'), $args) || in_array(array('nocache' => "'true'"), $args)
        ) {
            $this->tag_nocache = true;
        }
        
        if (($_output = $this->callTagCompiler($tag, $args, $parameter)) === false) {
            if (isset($this->smarty->template_functions[$tag])) {
                
                $args['_attr']['name'] = "'" . $tag . "'";
                $_output = $this->callTagCompiler('call', $args, $parameter);
            }
        }
        if ($_output !== false) {
            if ($_output !== true) {
                
                if ($this->has_code) {
                    
                    if ($this->has_output) {
                        $_output .= "\n";
                    }
                    
                    return $_output;
                }
            }
            
            return null;
        } else {
            
            if (isset($args['_attr'])) {
                foreach ($args['_attr'] as $key => $attribute) {
                    if (is_array($attribute)) {
                        $args = array_merge($args, $attribute);
                    }
                }
            }
            
            if (strlen($tag) < 6 || substr($tag, -5) != 'close') {
                
                if (isset($this->smarty->registered_objects[$tag]) && isset($parameter['object_methode'])) {
                    $methode = $parameter['object_methode'];
                    if (!in_array($methode, $this->smarty->registered_objects[$tag][3]) &&
                        (empty($this->smarty->registered_objects[$tag][1]) || in_array($methode, $this->smarty->registered_objects[$tag][1]))
                    ) {
                        return $this->callTagCompiler('private_object_function', $args, $parameter, $tag, $methode);
                    } elseif (in_array($methode, $this->smarty->registered_objects[$tag][3])) {
                        return $this->callTagCompiler('private_object_block_function', $args, $parameter, $tag, $methode);
                    } else {
                        return $this->trigger_template_error('unallowed methode "' . $methode . '" in registered object "' . $tag . '"', $this->lex->taglineno);
                    }
                }
                
                foreach (array(Smarty::PLUGIN_COMPILER, Smarty::PLUGIN_FUNCTION, Smarty::PLUGIN_BLOCK) as $plugin_type) {
                    if (isset($this->smarty->registered_plugins[$plugin_type][$tag])) {
                        
                        if ($plugin_type == Smarty::PLUGIN_COMPILER) {
                            $new_args = array();
                            foreach ($args as $key => $mixed) {
                                if (is_array($mixed)) {
                                    $new_args = array_merge($new_args, $mixed);
                                } else {
                                    $new_args[$key] = $mixed;
                                }
                            }
                            if (!$this->smarty->registered_plugins[$plugin_type][$tag][1]) {
                                $this->tag_nocache = true;
                            }
                            $function = $this->smarty->registered_plugins[$plugin_type][$tag][0];
                            if (!is_array($function)) {
                                return $function($new_args, $this);
                            } elseif (is_object($function[0])) {
                                return $this->smarty->registered_plugins[$plugin_type][$tag][0][0]->$function[1]($new_args, $this);
                            } else {
                                return call_user_func_array($function, array($new_args, $this));
                            }
                        }
                        
                        if ($plugin_type == Smarty::PLUGIN_FUNCTION || $plugin_type == Smarty::PLUGIN_BLOCK) {
                            return $this->callTagCompiler('private_registered_' . $plugin_type, $args, $parameter, $tag);
                        }
                    }
                }
                
                foreach ($this->smarty->plugin_search_order as $plugin_type) {
                    if ($plugin_type == Smarty::PLUGIN_COMPILER && $this->smarty->loadPlugin('smarty_compiler_' . $tag) && (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this))) {
                        $plugin = 'smarty_compiler_' . $tag;
                        if (is_callable($plugin)) {
                            
                            $new_args = array();
                            foreach ($args as $key => $mixed) {
                                if (is_array($mixed)) {
                                    $new_args = array_merge($new_args, $mixed);
                                } else {
                                    $new_args[$key] = $mixed;
                                }
                            }

                            return $plugin($new_args, $this->smarty);
                        }
                        if (class_exists($plugin, false)) {
                            $plugin_object = new $plugin;
                            if (method_exists($plugin_object, 'compile')) {
                                return $plugin_object->compile($args, $this);
                            }
                        }
                        throw new SmartyException("Plugin \"{$tag}\" not callable");
                    } else {
                        if ($function = $this->getPlugin($tag, $plugin_type)) {
                            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
                                return $this->callTagCompiler('private_' . $plugin_type . '_plugin', $args, $parameter, $tag, $function);
                            }
                        }
                    }
                }
                if (is_callable($this->smarty->default_plugin_handler_func)) {
                    $found = false;
                    
                    foreach ($this->smarty->plugin_search_order as $plugin_type) {
                        if (isset($this->default_handler_plugins[$plugin_type][$tag])) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        
                        foreach ($this->smarty->plugin_search_order as $plugin_type) {
                            if ($this->getPluginFromDefaultHandler($tag, $plugin_type)) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ($found) {
                        
                        if ($plugin_type == Smarty::PLUGIN_COMPILER) {
                            $new_args = array();
                            foreach ($args as $mixed) {
                                $new_args = array_merge($new_args, $mixed);
                            }
                            $function = $this->default_handler_plugins[$plugin_type][$tag][0];
                            if (!is_array($function)) {
                                return $function($new_args, $this);
                            } elseif (is_object($function[0])) {
                                return $this->default_handler_plugins[$plugin_type][$tag][0][0]->$function[1]($new_args, $this);
                            } else {
                                return call_user_func_array($function, array($new_args, $this));
                            }
                        } else {
                            return $this->callTagCompiler('private_registered_' . $plugin_type, $args, $parameter, $tag);
                        }
                    }
                }
            } else {
                
                $base_tag = substr($tag, 0, -5);
                
                if (isset($this->smarty->registered_objects[$base_tag]) && isset($parameter['object_methode'])) {
                    $methode = $parameter['object_methode'];
                    if (in_array($methode, $this->smarty->registered_objects[$base_tag][3])) {
                        return $this->callTagCompiler('private_object_block_function', $args, $parameter, $tag, $methode);
                    } else {
                        return $this->trigger_template_error('unallowed closing tag methode "' . $methode . '" in registered object "' . $base_tag . '"', $this->lex->taglineno);
                    }
                }
                
                if (isset($this->smarty->registered_plugins[Smarty::PLUGIN_BLOCK][$base_tag]) || isset($this->default_handler_plugins[Smarty::PLUGIN_BLOCK][$base_tag])) {
                    return $this->callTagCompiler('private_registered_block', $args, $parameter, $tag);
                }
                
                if ($function = $this->getPlugin($base_tag, Smarty::PLUGIN_BLOCK)) {
                    return $this->callTagCompiler('private_block_plugin', $args, $parameter, $tag, $function);
                }
                
                if (isset($this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag])) {
                    
                    $args = array();
                    if (!$this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][1]) {
                        $this->tag_nocache = true;
                    }
                    $function = $this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][0];
                    if (!is_array($function)) {
                        return $function($args, $this);
                    } elseif (is_object($function[0])) {
                        return $this->smarty->registered_plugins[Smarty::PLUGIN_COMPILER][$tag][0][0]->$function[1]($args, $this);
                    } else {
                        return call_user_func_array($function, array($args, $this));
                    }
                }
                if ($this->smarty->loadPlugin('smarty_compiler_' . $tag)) {
                    $plugin = 'smarty_compiler_' . $tag;
                    if (is_callable($plugin)) {
                        return $plugin($args, $this->smarty);
                    }
                    if (class_exists($plugin, false)) {
                        $plugin_object = new $plugin;
                        if (method_exists($plugin_object, 'compile')) {
                            return $plugin_object->compile($args, $this);
                        }
                    }
                    throw new SmartyException("Plugin \"{$tag}\" not callable");
                }
            }
            $this->trigger_template_error("unknown tag \"" . $tag . "\"", $this->lex->taglineno);
        }
    }

    
    public function callTagCompiler($tag, $args, $param1 = null, $param2 = null, $param3 = null)
    {
        
        if (isset(self::$_tag_objects[$tag])) {
            
            return self::$_tag_objects[$tag]->compile($args, $this, $param1, $param2, $param3);
        }
        
        $class_name = 'Smarty_Internal_Compile_' . $tag;
        if ($this->smarty->loadPlugin($class_name)) {
            
            if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
                
                self::$_tag_objects[$tag] = new $class_name;
                
                return self::$_tag_objects[$tag]->compile($args, $this, $param1, $param2, $param3);
            }
        }
        
        return false;
    }

    
    public function getPlugin($plugin_name, $plugin_type)
    {
        $function = null;
        if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
            if (isset($this->template->required_plugins['nocache'][$plugin_name][$plugin_type])) {
                $function = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'];
            } elseif (isset($this->template->required_plugins['compiled'][$plugin_name][$plugin_type])) {
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type] = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type];
                $function = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'];
            }
        } else {
            if (isset($this->template->required_plugins['compiled'][$plugin_name][$plugin_type])) {
                $function = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'];
            } elseif (isset($this->template->required_plugins['nocache'][$plugin_name][$plugin_type])) {
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type] = $this->template->required_plugins['nocache'][$plugin_name][$plugin_type];
                $function = $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'];
            }
        }
        if (isset($function)) {
            if ($plugin_type == 'modifier') {
                $this->modifier_plugins[$plugin_name] = true;
            }

            return $function;
        }
        
        $function = 'smarty_' . $plugin_type . '_' . $plugin_name;
        $file = $this->smarty->loadPlugin($function, false);

        if (is_string($file)) {
            if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['file'] = $file;
                $this->template->required_plugins['nocache'][$plugin_name][$plugin_type]['function'] = $function;
            } else {
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['file'] = $file;
                $this->template->required_plugins['compiled'][$plugin_name][$plugin_type]['function'] = $function;
            }
            if ($plugin_type == 'modifier') {
                $this->modifier_plugins[$plugin_name] = true;
            }

            return $function;
        }
        if (is_callable($function)) {
            
            return $function;
        }

        return false;
    }

    
    public function getPluginFromDefaultHandler($tag, $plugin_type)
    {
        $callback = null;
        $script = null;
        $cacheable = true;
        $result = call_user_func_array(
            $this->smarty->default_plugin_handler_func, array($tag, $plugin_type, $this->template, &$callback, &$script, &$cacheable)
        );
        if ($result) {
            $this->tag_nocache = $this->tag_nocache || !$cacheable;
            if ($script !== null) {
                if (is_file($script)) {
                    if ($this->template->caching && ($this->nocache || $this->tag_nocache)) {
                        $this->template->required_plugins['nocache'][$tag][$plugin_type]['file'] = $script;
                        $this->template->required_plugins['nocache'][$tag][$plugin_type]['function'] = $callback;
                    } else {
                        $this->template->required_plugins['compiled'][$tag][$plugin_type]['file'] = $script;
                        $this->template->required_plugins['compiled'][$tag][$plugin_type]['function'] = $callback;
                    }
                    include_once $script;
                } else {
                    $this->trigger_template_error("Default plugin handler: Returned script file \"{$script}\" for \"{$tag}\" not found");
                }
            }
            if (!is_string($callback) && !(is_array($callback) && is_string($callback[0]) && is_string($callback[1]))) {
                $this->trigger_template_error("Default plugin handler: Returned callback for \"{$tag}\" must be a static function name or array of class and function name");
            }
            if (is_callable($callback)) {
                $this->default_handler_plugins[$plugin_type][$tag] = array($callback, true, array());

                return true;
            } else {
                $this->trigger_template_error("Default plugin handler: Returned callback for \"{$tag}\" not callable");
            }
        }

        return false;
    }

    
    public function processNocacheCode($content, $is_code)
    {
        
        if ($is_code && !empty($content)) {
            
            if ((!($this->template->source->recompiled) || $this->forceNocache) && $this->template->caching && !$this->suppressNocacheProcessing &&
                ($this->nocache || $this->tag_nocache)
            ) {
                $this->template->has_nocache_code = true;
                $_output = addcslashes($content, '\'\\');
                $_output = str_replace("^#^", "'", $_output);
                $_output = "<?php echo '" . $_output . "';?>\n";
                
                foreach ($this->modifier_plugins as $plugin_name => $dummy) {
                    if (isset($this->template->required_plugins['compiled'][$plugin_name]['modifier'])) {
                        $this->template->required_plugins['nocache'][$plugin_name]['modifier'] = $this->template->required_plugins['compiled'][$plugin_name]['modifier'];
                    }
                }
            } else {
                $_output = $content;
            }
        } else {
            $_output = $content;
        }
        $this->modifier_plugins = array();
        $this->suppressNocacheProcessing = false;
        $this->tag_nocache = false;

        return $_output;
    }

    
    public function pushTrace($file, $uid, $line, $debug = true)
    {
        if ($this->smarty->debugging && $debug) {
            Smarty_Internal_Debug::end_compile($this->template);
        }
        array_push($this->trace_stack, array($this->smarty->_current_file, $this->trace_filepath, $this->trace_uid, $this->trace_line_offset));
        $this->trace_filepath = $this->smarty->_current_file = $file;
        $this->trace_uid = $uid;
        $this->trace_line_offset = $line ;
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::start_compile($this->template);
        }
    }

    
    public function popTrace()
    {
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::end_compile($this->template);
        }
        $r = array_pop($this->trace_stack);
        $this->smarty->_current_file = $r[0];
        $this->trace_filepath = $r[1];
        $this->trace_uid = $r[2];
        $this->trace_line_offset = $r[3];
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::start_compile($this->template);
        }
    }

    
    public function trigger_template_error($args = null, $line = null)
    {
        
        if (!isset($line)) {
            $line = $this->lex->line;
        }

        $match = preg_split("/\n/", $this->lex->data);
        $error_text = 'Syntax error in template "' . (empty($this->trace_filepath) ? $this->template->source->filepath : $this->trace_filepath) . '"  on line ' . ($line + $this->trace_line_offset)  . ' "' . trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1])) . '" ';
        if (isset($args)) {
            
            $error_text .= $args;
        } else {
            
            $error_text .= ' - Unexpected "' . $this->lex->value . '"';
            if (count($this->parser->yy_get_expected_tokens($this->parser->yymajor)) <= 4) {
                foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                    $exp_token = $this->parser->yyTokenName[$token];
                    if (isset($this->lex->smarty_token_names[$exp_token])) {
                        
                        $expect[] = '"' . $this->lex->smarty_token_names[$exp_token] . '"';
                    } else {
                        
                        $expect[] = $this->parser->yyTokenName[$token];
                    }
                }
                $error_text .= ', expected one of: ' . implode(' , ', $expect);
            }
        }
        $e = new SmartyCompilerException($error_text);
        $e->line = $line;
        $e->source = trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1]));
        $e->desc = $args;
        $e->template = $this->template->source->filepath;
        throw $e;
    }

}
