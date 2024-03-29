<?php



class Smarty_Internal_Compile_Private_Object_Function extends Smarty_Internal_CompileBase
{
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler, $parameter, $tag, $method)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->tag_nocache = true;
        }
        unset($_attr['nocache']);
        $_assign = null;
        if (isset($_attr['assign'])) {
            $_assign = $_attr['assign'];
            unset($_attr['assign']);
        }
        
        if (method_exists($compiler->smarty->registered_objects[$tag][0], $method)) {
            
            if ($compiler->smarty->registered_objects[$tag][2]) {
                $_paramsArray = array();
                foreach ($_attr as $_key => $_value) {
                    if (is_int($_key)) {
                        $_paramsArray[] = "$_key=>$_value";
                    } else {
                        $_paramsArray[] = "'$_key'=>$_value";
                    }
                }
                $_params = 'array(' . implode(",", $_paramsArray) . ')';
                $return = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}({$_params},\$_smarty_tpl)";
            } else {
                $_params = implode(",", $_attr);
                $return = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}({$_params})";
            }
        } else {
            
            $return = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}";
        }

        if (empty($_assign)) {
            
            $compiler->has_output = true;
            $output = "<?php echo {$return};?>\n";
        } else {
            $output = "<?php \$_smarty_tpl->assign({$_assign},{$return});?>\n";
        }

        return $output;
    }

}
