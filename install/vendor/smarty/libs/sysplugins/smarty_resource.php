<?php



abstract class Smarty_Resource
{
    
    public static $sources = array();
    
    public static $compileds = array();
    
    public static $resources = array();
    
    protected static $sysplugins = array(
        'file' => true,
        'string' => true,
        'extends' => true,
        'stream' => true,
        'eval' => true,
        'php' => true
    );

    
    public $compiler_class = 'Smarty_Internal_SmartyTemplateCompiler';

    
    public $template_lexer_class = 'Smarty_Internal_Templatelexer';

    
    public $template_parser_class = 'Smarty_Internal_Templateparser';

    
    abstract public function getContent(Smarty_Template_Source $source);

    
    abstract public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null);

    
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        
    }

    
    protected function buildUniqueResourceName(Smarty $smarty, $resource_name, $is_config = false)
    {
        if ($is_config) {
            return get_class($this) . '#' . $smarty->joined_config_dir . '#' . $resource_name;
        } else {
            return get_class($this) . '#' . $smarty->joined_template_dir . '#' . $resource_name;
        }
    }

    
    public function populateCompiledFilepath(Smarty_Template_Compiled $compiled, Smarty_Internal_Template $_template)
    {
        $_compile_id = isset($_template->compile_id) ? preg_replace('![^\w\|]+!', '_', $_template->compile_id) : null;
        $_filepath = $compiled->source->uid;
        
        if ($_template->smarty->use_sub_dirs) {
            $_filepath = substr($_filepath, 0, 2) . DS
             . substr($_filepath, 2, 2) . DS
             . substr($_filepath, 4, 2) . DS
             . $_filepath;
        }
        $_compile_dir_sep = $_template->smarty->use_sub_dirs ? DS : '^';
        if (isset($_compile_id)) {
            $_filepath = $_compile_id . $_compile_dir_sep . $_filepath;
        }
        
        if ($_template->caching) {
            $_cache = '.cache';
        } else {
            $_cache = '';
        }
        $_compile_dir = $_template->smarty->getCompileDir();
        
        $_basename = $this->getBasename($compiled->source);
        if ($_basename === null) {
            $_basename = basename( preg_replace('![^\w\/]+!', '_', $compiled->source->name) );
        }
        
        if ($_basename) {
            $_basename = '.' . $_basename;
        }

        $compiled->filepath = $_compile_dir . $_filepath . '.' . $compiled->source->type . $_basename . $_cache . '.php';
    }

    
    protected function normalizePath($_path, $ds=true)
    {
        if ($ds) {
            
            $_path = str_replace('\\', '/', $_path);
        }

        $offset = 0;

        
        $_path = preg_replace('#/\./(\./)*#', '/', $_path);
        
        while (true) {
            $_parent = strpos($_path, '/../', $offset);
            if (!$_parent) {
                break;
            } elseif ($_path[$_parent - 1] === '.') {
                $offset = $_parent + 3;
                continue;
            }

            $_pos = strrpos($_path, '/', $_parent - strlen($_path) - 1);
            if ($_pos === false) {
                
                $_pos = $_parent;
            }

            $_path = substr_replace($_path, '', $_pos, $_parent + 3 - $_pos);
        }

        if ($ds && DS != '/') {
            
            $_path = str_replace('/', '\\', $_path);
        }

        return $_path;
    }

    
    protected function buildFilepath(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $file = $source->name;
        if ($source instanceof Smarty_Config_Source) {
            $_directories = $source->smarty->getConfigDir();
            $_default_handler = $source->smarty->default_config_handler_func;
        } else {
            $_directories = $source->smarty->getTemplateDir();
            $_default_handler = $source->smarty->default_template_handler_func;
        }

        
        $_file_is_dotted = $file[0] == '.' && ($file[1] == '.' || $file[1] == '/' || $file[1] == "\\");
        if ($_template && $_template->parent instanceof Smarty_Internal_Template && $_file_is_dotted) {
            if ($_template->parent->source->type != 'file' && $_template->parent->source->type != 'extends' && !$_template->parent->allow_relative_path) {
                throw new SmartyException("Template '{$file}' cannot be relative to template of resource type '{$_template->parent->source->type}'");
            }
            $file = dirname($_template->parent->source->filepath) . DS . $file;
            $_file_exact_match = true;
            if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $file)) {
                
                
                $file = getcwd() . DS . $file;
            }
        }

        
        if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $file)) {
            
            $_path = str_replace('\\', '/', $file);
            $_path = DS . trim($file, '/');
            $_was_relative = true;
        } else {
            
            $_path = str_replace('\\', '/', $file);
        }
        $_path = $this->normalizePath($_path, false);
        if (DS != '/') {
            
            $_path = str_replace('/', '\\', $_path);
        }
        
        if (isset($_was_relative)) {
            $_path = substr($_path, 1);
        }

        
        $file = rtrim($_path, '/\\');

        
        if (isset($_file_exact_match)) {
            return $this->fileExists($source, $file) ? $file : false;
        }

        
        if (preg_match('#^\[(?P<key>[^\]]+)\](?P<file>.+)$#', $file, $match)) {
            $_directory = null;
            
            if (isset($_directories[$match['key']])) {
                $_directory = $_directories[$match['key']];
            } elseif (is_numeric($match['key'])) {
                
                $match['key'] = (int) $match['key'];
                if (isset($_directories[$match['key']])) {
                    $_directory = $_directories[$match['key']];
                } else {
                    
                    $keys = array_keys($_directories);
                    $_directory = $_directories[$keys[$match['key']]];
                }
            }

            if ($_directory) {
                $_file = substr($file, strpos($file, ']') + 1);
                $_filepath = $_directory . $_file;
                if ($this->fileExists($source, $_filepath)) {
                    return $_filepath;
                }
            }
        }

        $_stream_resolve_include_path = function_exists('stream_resolve_include_path');

        
        if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $file)) {
            foreach ($_directories as $_directory) {
                $_filepath = $_directory . $file;
                if ($this->fileExists($source, $_filepath)) {
                    return $this->normalizePath($_filepath);
                }
                if ($source->smarty->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_directory)) {
                    
                    if ($_stream_resolve_include_path) {
                        $_filepath = stream_resolve_include_path($_filepath);
                    } else {
                        $_filepath = Smarty_Internal_Get_Include_Path::getIncludePath($_filepath);
                    }

                    if ($_filepath !== false) {
                        if ($this->fileExists($source, $_filepath)) {
                            return $this->normalizePath($_filepath);
                        }
                    }
                }
            }
        }

        
        if ($this->fileExists($source, $file)) {
            return $file;
        }

        
        if ($_default_handler) {
            if (!is_callable($_default_handler)) {
                if ($source instanceof Smarty_Config_Source) {
                    throw new SmartyException("Default config handler not callable");
                } else {
                    throw new SmartyException("Default template handler not callable");
                }
            }
            $_return = call_user_func_array($_default_handler,
                array($source->type, $source->name, &$_content, &$_timestamp, $source->smarty));
            if (is_string($_return)) {
                $source->timestamp = @filemtime($_return);
                $source->exists = !!$source->timestamp;

                return $_return;
            } elseif ($_return === true) {
                $source->content = $_content;
                $source->timestamp = $_timestamp;
                $source->exists = true;

                return $_filepath;
            }
        }

        
        return false;
    }

    
    protected function fileExists(Smarty_Template_Source $source, $file)
    {
        $source->timestamp = is_file($file) ? @filemtime($file) : false;

        return $source->exists = !!$source->timestamp;

    }

    
    protected function getBasename(Smarty_Template_Source $source)
    {
        return null;
    }

    
    public static function load(Smarty $smarty, $type)
    {
        
        if (isset($smarty->_resource_handlers[$type])) {
            return $smarty->_resource_handlers[$type];
        }

        
        if (isset($smarty->registered_resources[$type])) {
            if ($smarty->registered_resources[$type] instanceof Smarty_Resource) {
                $smarty->_resource_handlers[$type] = $smarty->registered_resources[$type];
                
                return $smarty->_resource_handlers[$type];
            }

            if (!isset(self::$resources['registered'])) {
                self::$resources['registered'] = new Smarty_Internal_Resource_Registered();
            }
            if (!isset($smarty->_resource_handlers[$type])) {
                $smarty->_resource_handlers[$type] = self::$resources['registered'];
            }

            return $smarty->_resource_handlers[$type];
        }

        
        if (isset(self::$sysplugins[$type])) {
            if (!isset(self::$resources[$type])) {
                $_resource_class = 'Smarty_Internal_Resource_' . ucfirst($type);
                self::$resources[$type] = new $_resource_class();
            }

            return $smarty->_resource_handlers[$type] = self::$resources[$type];
        }

        
        $_resource_class = 'Smarty_Resource_' . ucfirst($type);
        if ($smarty->loadPlugin($_resource_class)) {
            if (isset(self::$resources[$type])) {
                return $smarty->_resource_handlers[$type] = self::$resources[$type];
            }

            if (class_exists($_resource_class, false)) {
                self::$resources[$type] = new $_resource_class();

                return $smarty->_resource_handlers[$type] = self::$resources[$type];
            } else {
                $smarty->registerResource($type, array(
                    "smarty_resource_{$type}_source",
                    "smarty_resource_{$type}_timestamp",
                    "smarty_resource_{$type}_secure",
                    "smarty_resource_{$type}_trusted"
                ));

                
                return self::load($smarty, $type);
            }
        }

        
        $_known_stream = stream_get_wrappers();
        if (in_array($type, $_known_stream)) {
            
            if (is_object($smarty->security_policy)) {
                $smarty->security_policy->isTrustedStream($type);
            }
            if (!isset(self::$resources['stream'])) {
                self::$resources['stream'] = new Smarty_Internal_Resource_Stream();
            }

            return $smarty->_resource_handlers[$type] = self::$resources['stream'];
        }

        

        
        throw new SmartyException("Unkown resource type '{$type}'");
    }

    
    protected static function parseResourceName($resource_name, $default_resource, &$name, &$type)
    {
        $parts = explode(':', $resource_name, 2);
        if (!isset($parts[1]) || !isset($parts[0][1])) {
            
            
            $type = $default_resource;
            $name = $resource_name;
        } else {
            $type = $parts[0];
            $name = $parts[1];
        }
    }

    

    
    public static function getUniqueTemplateName($template, $template_resource)
    {
        self::parseResourceName($template_resource, $template->smarty->default_resource_type, $name, $type);
        
        $resource = Smarty_Resource::load($template->smarty, $type);
        
        $_file_is_dotted = $name[0] == '.' && ($name[1] == '.' || $name[1] == '/' || $name[1] == "\\");
        if ($template instanceof Smarty_Internal_Template && $_file_is_dotted && ($template->source->type == 'file' || $template->parent->source->type == 'extends')) {
            $name = dirname($template->source->filepath) . DS . $name;
        }
        return $resource->buildUniqueResourceName($template->smarty, $name);
    }

    
    public static function source(Smarty_Internal_Template $_template=null, Smarty $smarty=null, $template_resource=null)
    {
        if ($_template) {
            $smarty = $_template->smarty;
            $template_resource = $_template->template_resource;
        }

        
        self::parseResourceName($template_resource, $smarty->default_resource_type, $name, $type);
        $resource = Smarty_Resource::load($smarty, $type);
        
        $_file_is_dotted = isset($name[0]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/' || $name[1] == "\\");
        if ($_file_is_dotted && isset($_template) && $_template->parent instanceof Smarty_Internal_Template && ($_template->parent->source->type == 'file' || $_template->parent->source->type == 'extends')) {
            $name2 = dirname($_template->parent->source->filepath) . DS . $name;
        } else {
            $name2 = $name;
        }
        $unique_resource_name = $resource->buildUniqueResourceName($smarty, $name2);

        
        $_cache_key = 'template|' . $unique_resource_name;
        if ($smarty->compile_id) {
            $_cache_key .= '|'.$smarty->compile_id;
        }
        if (isset(self::$sources[$_cache_key])) {
            return self::$sources[$_cache_key];
        }

        
        $source = new Smarty_Template_Source($resource, $smarty, $template_resource, $type, $name, $unique_resource_name);
        $resource->populate($source, $_template);

        
        self::$sources[$_cache_key] = $source;

        return $source;
    }

    
    public static function config(Smarty_Internal_Config $_config)
    {
        static $_incompatible_resources = array('eval' => true, 'string' => true, 'extends' => true, 'php' => true);
        $config_resource = $_config->config_resource;
        $smarty = $_config->smarty;

        
        self::parseResourceName($config_resource, $smarty->default_config_type, $name, $type);

        
        if (isset($_incompatible_resources[$type])) {
            throw new SmartyException ("Unable to use resource '{$type}' for config");
        }

        
        $resource = Smarty_Resource::load($smarty, $type);
        $unique_resource_name = $resource->buildUniqueResourceName($smarty, $name, true);

        
        $_cache_key = 'config|' . $unique_resource_name;
        if (isset(self::$sources[$_cache_key])) {
            return self::$sources[$_cache_key];
        }

        
        $source = new Smarty_Config_Source($resource, $smarty, $config_resource, $type, $name, $unique_resource_name);
        $resource->populate($source, null);

        
        self::$sources[$_cache_key] = $source;

        return $source;
    }

}


class Smarty_Template_Source
{
    
    public $compiler_class = null;

    
    public $template_lexer_class = null;

    
    public $template_parser_class = null;

    
    public $uid = null;

    
    public $resource = null;

    
    public $type = null;

    
    public $name = null;

    
    public $unique_resource = null;

    
    public $filepath = null;

    
    public $uncompiled = null;

    
    public $recompiled = null;

    
    public $components = null;

    
    public $handler = null;

    
    public $smarty = null;

    
    public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name, $unique_resource)
    {
        $this->handler = $handler; 

        $this->compiler_class = $handler->compiler_class;
        $this->template_lexer_class = $handler->template_lexer_class;
        $this->template_parser_class = $handler->template_parser_class;
        $this->uncompiled = $this->handler instanceof Smarty_Resource_Uncompiled;
        $this->recompiled = $this->handler instanceof Smarty_Resource_Recompiled;

        $this->smarty = $smarty;
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
        $this->unique_resource = $unique_resource;
    }

    
    public function getCompiled(Smarty_Internal_Template $_template)
    {
        
        $_cache_key = $this->unique_resource . '#' . $_template->compile_id;
        if (isset(Smarty_Resource::$compileds[$_cache_key])) {
            return Smarty_Resource::$compileds[$_cache_key];
        }

        $compiled = new Smarty_Template_Compiled($this);
        $this->handler->populateCompiledFilepath($compiled, $_template);
        $compiled->timestamp = @filemtime($compiled->filepath);
        $compiled->exists = !!$compiled->timestamp;

        
        Smarty_Resource::$compileds[$_cache_key] = $compiled;

        return $compiled;
    }

    
    public function renderUncompiled(Smarty_Internal_Template $_template)
    {
        return $this->handler->renderUncompiled($this, $_template);
    }

    
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            
            case 'timestamp':
            case 'exists':
            case 'content':
            
            case 'template':
                $this->$property_name = $value;
                break;

            default:
                throw new SmartyException("invalid source property '$property_name'.");
        }
    }

    
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'timestamp':
            case 'exists':
                $this->handler->populateTimestamp($this);

                return $this->$property_name;

            case 'content':
                return $this->content = $this->handler->getContent($this);

            default:
                throw new SmartyException("source property '$property_name' does not exist.");
        }
    }

}


class Smarty_Template_Compiled
{
    
    public $filepath = null;

    
    public $timestamp = null;

    
    public $exists = false;

    
    public $loaded = false;

    
    public $isCompiled = false;

    
    public $source = null;

    
    public $_properties = null;

    
    public function __construct(Smarty_Template_Source $source)
    {
        $this->source = $source;
    }

}
