<?php





class Smarty_Security
{
    
    public $php_handling = Smarty::PHP_PASSTHRU;
    
    public $secure_dir = array();
    
    public $trusted_dir = array();
    
    public $trusted_uri = array();
    
    public $static_classes = array();
    
    public $php_functions = array(
        'isset', 'empty',
        'count', 'sizeof',
        'in_array', 'is_array',
        'time',
        'nl2br',
    );
    
    public $php_modifiers = array(
        'escape',
        'count'
    );
    
    public $allowed_tags = array();
    
    public $disabled_tags = array();
    
    public $allowed_modifiers = array();
    
    public $disabled_modifiers = array();
    
    public $streams = array('file');
    
    public $allow_constants = true;
    
    public $allow_super_globals = true;

    
    protected $_resource_dir = null;
    
    protected $_template_dir = null;
    
    protected $_config_dir = null;
    
    protected $_secure_dir = null;
    
    protected $_php_resource_dir = null;
    
    protected $_trusted_dir = null;

    
    public function __construct($smarty)
    {
        $this->smarty = $smarty;
    }

    
    public function isTrustedPhpFunction($function_name, $compiler)
    {
        if (isset($this->php_functions) && (empty($this->php_functions) || in_array($function_name, $this->php_functions))) {
            return true;
        }

        $compiler->trigger_template_error("PHP function '{$function_name}' not allowed by security setting");

        return false; 
    }

    
    public function isTrustedStaticClass($class_name, $compiler)
    {
        if (isset($this->static_classes) && (empty($this->static_classes) || in_array($class_name, $this->static_classes))) {
            return true;
        }

        $compiler->trigger_template_error("access to static class '{$class_name}' not allowed by security setting");

        return false; 
    }

    
    public function isTrustedPhpModifier($modifier_name, $compiler)
    {
        if (isset($this->php_modifiers) && (empty($this->php_modifiers) || in_array($modifier_name, $this->php_modifiers))) {
            return true;
        }

        $compiler->trigger_template_error("modifier '{$modifier_name}' not allowed by security setting");

        return false; 
    }

    
    public function isTrustedTag($tag_name, $compiler)
    {
        
        if (in_array($tag_name, array('assign', 'call', 'private_filter', 'private_block_plugin', 'private_function_plugin', 'private_object_block_function',
                    'private_object_function', 'private_registered_function', 'private_registered_block', 'private_special_variable', 'private_print_expression', 'private_modifier'))) {
            return true;
        }
        
        if (empty($this->allowed_tags)) {
            if (empty($this->disabled_tags) || !in_array($tag_name, $this->disabled_tags)) {
                return true;
            } else {
                $compiler->trigger_template_error("tag '{$tag_name}' disabled by security setting", $compiler->lex->taglineno);
            }
        } elseif (in_array($tag_name, $this->allowed_tags) && !in_array($tag_name, $this->disabled_tags)) {
            return true;
        } else {
            $compiler->trigger_template_error("tag '{$tag_name}' not allowed by security setting", $compiler->lex->taglineno);
        }

        return false; 
    }

    
    public function isTrustedModifier($modifier_name, $compiler)
    {
        
        if (in_array($modifier_name, array('default'))) {
            return true;
        }
        
        if (empty($this->allowed_modifiers)) {
            if (empty($this->disabled_modifiers) || !in_array($modifier_name, $this->disabled_modifiers)) {
                return true;
            } else {
                $compiler->trigger_template_error("modifier '{$modifier_name}' disabled by security setting", $compiler->lex->taglineno);
            }
        } elseif (in_array($modifier_name, $this->allowed_modifiers) && !in_array($modifier_name, $this->disabled_modifiers)) {
            return true;
        } else {
            $compiler->trigger_template_error("modifier '{$modifier_name}' not allowed by security setting", $compiler->lex->taglineno);
        }

        return false; 
    }

    
    public function isTrustedStream($stream_name)
    {
        if (isset($this->streams) && (empty($this->streams) || in_array($stream_name, $this->streams))) {
            return true;
        }

        throw new SmartyException("stream '{$stream_name}' not allowed by security setting");
    }

    
    public function isTrustedResourceDir($filepath)
    {
        $_template = false;
        $_config = false;
        $_secure = false;

        $_template_dir = $this->smarty->getTemplateDir();
        $_config_dir = $this->smarty->getConfigDir();

        
        if ((!$this->_template_dir || $this->_template_dir !== $_template_dir)
                || (!$this->_config_dir || $this->_config_dir !== $_config_dir)
                || (!empty($this->secure_dir) && (!$this->_secure_dir || $this->_secure_dir !== $this->secure_dir))
        ) {
            $this->_resource_dir = array();
            $_template = true;
            $_config = true;
            $_secure = !empty($this->secure_dir);
        }

        
        if ($_template) {
            $this->_template_dir = $_template_dir;
            foreach ($_template_dir as $directory) {
                $directory = realpath($directory);
                $this->_resource_dir[$directory] = true;
            }
        }

        
        if ($_config) {
            $this->_config_dir = $_config_dir;
            foreach ($_config_dir as $directory) {
                $directory = realpath($directory);
                $this->_resource_dir[$directory] = true;
            }
        }

        
        if ($_secure) {
            $this->_secure_dir = $this->secure_dir;
            foreach ((array) $this->secure_dir as $directory) {
                $directory = realpath($directory);
                $this->_resource_dir[$directory] = true;
            }
        }

        $_filepath = realpath($filepath);
        $directory = dirname($_filepath);
        $_directory = array();
        while (true) {
            
            $_directory[$directory] = true;
            
            if (isset($this->_resource_dir[$directory])) {
                
                $this->_resource_dir = array_merge($this->_resource_dir, $_directory);

                return true;
            }
            
            if (($pos = strrpos($directory, DS)) === false || !isset($directory[1])) {
                break;
            }
            
            $directory = substr($directory, 0, $pos);
        }

        
        throw new SmartyException("directory '{$_filepath}' not allowed by security setting");
    }

    
    public function isTrustedUri($uri)
    {
        $_uri = parse_url($uri);
        if (!empty($_uri['scheme']) && !empty($_uri['host'])) {
            $_uri = $_uri['scheme'] . ':
            foreach ($this->trusted_uri as $pattern) {
                if (preg_match($pattern, $_uri)) {
                    return true;
                }
            }
        }

        throw new SmartyException("URI '{$uri}' not allowed by security setting");
    }

    
    public function isTrustedPHPDir($filepath)
    {
        if (empty($this->trusted_dir)) {
            throw new SmartyException("directory '{$filepath}' not allowed by security setting (no trusted_dir specified)");
        }

        
        if (!$this->_trusted_dir || $this->_trusted_dir !== $this->trusted_dir) {
            $this->_php_resource_dir = array();

            $this->_trusted_dir = $this->trusted_dir;
            foreach ((array) $this->trusted_dir as $directory) {
                $directory = realpath($directory);
                $this->_php_resource_dir[$directory] = true;
            }
        }

        $_filepath = realpath($filepath);
        $directory = dirname($_filepath);
        $_directory = array();
        while (true) {
            
            $_directory[] = $directory;
            
            if (isset($this->_php_resource_dir[$directory])) {
                
                $this->_php_resource_dir = array_merge($this->_php_resource_dir, $_directory);

                return true;
            }
            
            if (($pos = strrpos($directory, DS)) === false || !isset($directory[2])) {
                break;
            }
            
            $directory = substr($directory, 0, $pos);
        }

        throw new SmartyException("directory '{$_filepath}' not allowed by security setting");
    }

}
