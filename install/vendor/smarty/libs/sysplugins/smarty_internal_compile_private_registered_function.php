<?php



class Smarty_Internal_Compile_Private_Registered_Function extends Smarty_Internal_CompileBase
{
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler, $parameter, $tag)
    {
        
        $compiler->has_output = true;
        
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache']) {
            $compiler->tag_nocache = true;
        }
        unset($_attr['nocache']);
               if (isset($compiler->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION][$tag])) {
                   $tag_info = $compiler->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION][$tag];
               } else {
                   $tag_info = $compiler->default_handler_plugins[Smarty::PLUGIN_FUNCTION][$tag];
               }
        
        $compiler->tag_nocache =  $compiler->tag_nocache || !$tag_info[1];
        
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = "$_key=>$_value";
            } elseif ($compiler->template->caching && in_array($_key,$tag_info[2])) {
                $_value = str_replace("'","^#^",$_value);
                $_paramsArray[] = "'$_key'=>^#^.var_export($_value,true).^#^";
            } else {
                $_paramsArray[] = "'$_key'=>$_value";
            }
        }
        $_params = 'array(' . implode(",", $_paramsArray) . ')';
        $function = $tag_info[0];
        
        if (!is_array($function)) {
            $output = "<?php echo {$function}({$_params},\$_smarty_tpl);?>\n";
        } elseif (is_object($function[0])) {
            $output = "<?php echo \$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['{$tag}'][0][0]->{$function[1]}({$_params},\$_smarty_tpl);?>\n";
        } else {
            $output = "<?php echo {$function[0]}::{$function[1]}({$_params},\$_smarty_tpl);?>\n";
        }

        return $output;
    }

}
