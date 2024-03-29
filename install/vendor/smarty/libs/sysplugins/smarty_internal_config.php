<?php



class Smarty_Internal_Config
{
    
    public $smarty = null;
    
    public $data = null;
    
    public $config_resource = null;
    
    public $compiled_config = null;
    
    public $compiled_filepath = null;
    
    public $compiled_timestamp = null;
    
    public $mustCompile = null;
    
    public $compiler_object = null;

    
    public function __construct($config_resource, $smarty, $data = null)
    {
        $this->data = $data;
        $this->smarty = $smarty;
        $this->config_resource = $config_resource;
    }

    
    public function getCompiledFilepath()
    {
        return $this->compiled_filepath === null ?
                ($this->compiled_filepath = $this->buildCompiledFilepath()) :
                $this->compiled_filepath;
    }

    
    public function buildCompiledFilepath()
    {
        $_compile_id = isset($this->smarty->compile_id) ? preg_replace('![^\w\|]+!', '_', $this->smarty->compile_id) : null;
        $_flag = (int) $this->smarty->config_read_hidden + (int) $this->smarty->config_booleanize * 2
                + (int) $this->smarty->config_overwrite * 4;
        $_filepath = sha1($this->source->filepath . $_flag);
        
        if ($this->smarty->use_sub_dirs) {
            $_filepath = substr($_filepath, 0, 2) . DS
                    . substr($_filepath, 2, 2) . DS
                    . substr($_filepath, 4, 2) . DS
                    . $_filepath;
        }
        $_compile_dir_sep = $this->smarty->use_sub_dirs ? DS : '^';
        if (isset($_compile_id)) {
            $_filepath = $_compile_id . $_compile_dir_sep . $_filepath;
        }
        $_compile_dir = $this->smarty->getCompileDir();

        return $_compile_dir . $_filepath . '.' . basename($this->source->name) . '.config' . '.php';
    }

    
    public function getCompiledTimestamp()
    {
        return $this->compiled_timestamp === null
            ? ($this->compiled_timestamp = (file_exists($this->getCompiledFilepath())) ? filemtime($this->getCompiledFilepath()) : false)
            : $this->compiled_timestamp;
    }

    
    public function mustCompile()
    {
        return $this->mustCompile === null ?
            $this->mustCompile = ($this->smarty->force_compile || $this->getCompiledTimestamp () === false || $this->smarty->compile_check && $this->getCompiledTimestamp () < $this->source->timestamp):
            $this->mustCompile;
    }

    
    public function getCompiledConfig()
    {
        if ($this->compiled_config === null) {
            
            if ($this->mustCompile()) {
                $this->compileConfigSource();
            } else {
                $this->compiled_config = file_get_contents($this->getCompiledFilepath());
            }
        }

        return $this->compiled_config;
    }

    
    public function compileConfigSource()
    {
        
        if (!is_object($this->compiler_object)) {
            
            $this->compiler_object = new Smarty_Internal_Config_File_Compiler($this->smarty);
        }
        
        if ($this->smarty->compile_locking) {
            if ($saved_timestamp = $this->getCompiledTimestamp()) {
                touch($this->getCompiledFilepath());
            }
        }
        
        try {
            $this->compiler_object->compileSource($this);
        } catch (Exception $e) {
            
            if ($this->smarty->compile_locking && $saved_timestamp) {
                touch($this->getCompiledFilepath(), $saved_timestamp);
            }
            throw $e;
        }
        
        
        Smarty_Internal_Write_File::writeFile($this->getCompiledFilepath(), $this->getCompiledConfig(), $this->smarty);
    }

    
    public function loadConfigVars($sections = null, $scope = 'local')
    {
        if ($this->data instanceof Smarty_Internal_Template) {
            $this->data->properties['file_dependency'][sha1($this->source->filepath)] = array($this->source->filepath, $this->source->timestamp, 'file');
        }
        if ($this->mustCompile()) {
            $this->compileConfigSource();
        }
        
        if ($scope == 'local') {
            $scope_ptr = $this->data;
        } elseif ($scope == 'parent') {
            if (isset($this->data->parent)) {
                $scope_ptr = $this->data->parent;
            } else {
                $scope_ptr = $this->data;
            }
        } elseif ($scope == 'root' || $scope == 'global') {
            $scope_ptr = $this->data;
            while (isset($scope_ptr->parent)) {
                $scope_ptr = $scope_ptr->parent;
            }
        }
        $_config_vars = array();
        include($this->getCompiledFilepath());
        
        foreach ($_config_vars['vars'] as $variable => $value) {
            if ($this->smarty->config_overwrite || !isset($scope_ptr->config_vars[$variable])) {
                $scope_ptr->config_vars[$variable] = $value;
            } else {
                $scope_ptr->config_vars[$variable] = array_merge((array) $scope_ptr->config_vars[$variable], (array) $value);
            }
        }
        
        if (!empty($sections)) {
            foreach ((array) $sections as $this_section) {
                if (isset($_config_vars['sections'][$this_section])) {
                    foreach ($_config_vars['sections'][$this_section]['vars'] as $variable => $value) {
                        if ($this->smarty->config_overwrite || !isset($scope_ptr->config_vars[$variable])) {
                            $scope_ptr->config_vars[$variable] = $value;
                        } else {
                            $scope_ptr->config_vars[$variable] = array_merge((array) $scope_ptr->config_vars[$variable], (array) $value);
                        }
                    }
                }
            }
        }
    }

    
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            case 'source':
            case 'compiled':
                $this->$property_name = $value;

                return;
        }

        throw new SmartyException("invalid config property '$property_name'.");
    }

    
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'source':
                if (empty($this->config_resource)) {
                    throw new SmartyException("Unable to parse resource name \"{$this->config_resource}\"");
                }
                $this->source = Smarty_Resource::config($this);

                return $this->source;

            case 'compiled':
                $this->compiled = $this->source->getCompiled($this);

                return $this->compiled;
        }

        throw new SmartyException("config attribute '$property_name' does not exist.");
    }

}
