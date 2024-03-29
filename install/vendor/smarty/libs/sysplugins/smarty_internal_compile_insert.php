<?php




class Smarty_Internal_Compile_Insert extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('name');
    
    public $shorttag_order = array('name');
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        
        $compiler->suppressNocacheProcessing = true;
        $compiler->tag_nocache = true;
        $_smarty_tpl = $compiler->template;
        $_name = null;
        $_script = null;

        $_output = '<?php ';
        
        eval('$_name = ' . $_attr['name'] . ';');
        if (isset($_attr['assign'])) {
            
            $_assign = $_attr['assign'];
            
            $compiler->template->tpl_vars[trim($_attr['assign'], "'")] = new Smarty_Variable(null, true);
        }
        if (isset($_attr['script'])) {
            
            $_function = "smarty_insert_{$_name}";
            $_smarty_tpl = $compiler->template;
            $_filepath = false;
            eval('$_script = ' . $_attr['script'] . ';');
            if (!isset($compiler->smarty->security_policy) && file_exists($_script)) {
                $_filepath = $_script;
            } else {
                if (isset($compiler->smarty->security_policy)) {
                    $_dir = $compiler->smarty->security_policy->trusted_dir;
                } else {
                    $_dir = $compiler->smarty->trusted_dir;
                }
                if (!empty($_dir)) {
                    foreach ((array) $_dir as $_script_dir) {
                        $_script_dir = rtrim($_script_dir, '/\\') . DS;
                        if (file_exists($_script_dir . $_script)) {
                            $_filepath = $_script_dir . $_script;
                            break;
                        }
                    }
                }
            }
            if ($_filepath == false) {
                $compiler->trigger_template_error("{insert} missing script file '{$_script}'", $compiler->lex->taglineno);
            }
            
            $_output .= "require_once '{$_filepath}' ;";
            require_once $_filepath;
            if (!is_callable($_function)) {
                $compiler->trigger_template_error(" {insert} function '{$_function}' is not callable in script file '{$_script}'", $compiler->lex->taglineno);
            }
        } else {
            $_filepath = 'null';
            $_function = "insert_{$_name}";
            
            if (!is_callable($_function)) {
                
                if (!$_function = $compiler->getPlugin($_name, 'insert')) {
                    $compiler->trigger_template_error("{insert} no function or plugin found for '{$_name}'", $compiler->lex->taglineno);
                }
            }
        }
        
        unset($_attr['name'], $_attr['assign'], $_attr['script'], $_attr['nocache']);
        
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            $_paramsArray[] = "'$_key' => $_value";
        }
        $_params = 'array(' . implode(", ", $_paramsArray) . ')';
        
        if (isset($_assign)) {
            if ($_smarty_tpl->caching) {
                $_output .= "echo Smarty_Internal_Nocache_Insert::compile ('{$_function}',{$_params}, \$_smarty_tpl, '{$_filepath}',{$_assign});?>";
            } else {
                $_output .= "\$_smarty_tpl->assign({$_assign} , {$_function} ({$_params},\$_smarty_tpl), true);?>";
            }
        } else {
            $compiler->has_output = true;
            if ($_smarty_tpl->caching) {
                $_output .= "echo Smarty_Internal_Nocache_Insert::compile ('{$_function}',{$_params}, \$_smarty_tpl, '{$_filepath}');?>";
            } else {
                $_output .= "echo {$_function}({$_params},\$_smarty_tpl);?>";
            }
        }

        return $_output;
    }

}
