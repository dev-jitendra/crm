<?php



class Smarty_Internal_Compile_Call extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('name');
    
    public $shorttag_order = array('name');
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        
        if (isset($_attr['assign'])) {
            
            $_assign = $_attr['assign'];
        }
        $_name = $_attr['name'];
        if ($compiler->compiles_template_function) {
            $compiler->called_functions[] = trim($_name, "'\"");
        }
        unset($_attr['name'], $_attr['assign'], $_attr['nocache']);
        
        if ($compiler->nocache || $compiler->tag_nocache) {
            $_nocache = 'true';
        } else {
            $_nocache = 'false';
        }
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = "$_key=>$_value";
            } else {
                $_paramsArray[] = "'$_key'=>$_value";
            }
        }
        if (isset($compiler->template->properties['function'][$_name]['parameter'])) {
            foreach ($compiler->template->properties['function'][$_name]['parameter'] as $_key => $_value) {
                if (!isset($_attr[$_key])) {
                    if (is_int($_key)) {
                        $_paramsArray[] = "$_key=>$_value";
                    } else {
                        $_paramsArray[] = "'$_key'=>$_value";
                    }
                }
            }
        } elseif (isset($compiler->smarty->template_functions[$_name]['parameter'])) {
            foreach ($compiler->smarty->template_functions[$_name]['parameter'] as $_key => $_value) {
                if (!isset($_attr[$_key])) {
                    if (is_int($_key)) {
                        $_paramsArray[] = "$_key=>$_value";
                    } else {
                        $_paramsArray[] = "'$_key'=>$_value";
                    }
                }
            }
        }
        
        if (!(strpos($_name, '$') === false)) {
            $call_cache = $_name;
            $call_function = '$tmp = "smarty_template_function_".' . $_name . '; $tmp';
        } else {
            $_name = trim($_name, "'\"");
            $call_cache = "'{$_name}'";
            $call_function = 'smarty_template_function_' . $_name;
        }

        $_params = 'array(' . implode(",", $_paramsArray) . ')';
        $_hash = str_replace('-', '_', $compiler->template->properties['nocache_hash']);
        
        if (isset($_assign)) {
            if ($compiler->template->caching) {
                $_output = "<?php ob_start(); Smarty_Internal_Function_Call_Handler::call ({$call_cache},\$_smarty_tpl,{$_params},'{$_hash}',{$_nocache}); \$_smarty_tpl->assign({$_assign}, ob_get_clean());?>\n";
            } else {
                $_output = "<?php ob_start(); {$call_function}(\$_smarty_tpl,{$_params}); \$_smarty_tpl->assign({$_assign}, ob_get_clean());?>\n";
            }
        } else {
            if ($compiler->template->caching) {
                $_output = "<?php Smarty_Internal_Function_Call_Handler::call ({$call_cache},\$_smarty_tpl,{$_params},'{$_hash}',{$_nocache});?>\n";
            } else {
                $_output = "<?php {$call_function}(\$_smarty_tpl,{$_params});?>\n";
            }
        }

        return $_output;
    }

}
