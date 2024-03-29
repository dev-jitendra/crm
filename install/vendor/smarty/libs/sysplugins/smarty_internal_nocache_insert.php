<?php



class Smarty_Internal_Nocache_Insert
{
    
    public static function compile($_function, $_attr, $_template, $_script, $_assign = null)
    {
        $_output = '<?php ';
        if ($_script != 'null') {
            
            
            $_output .= "require_once '{$_script}';";
        }
        
        if (isset($_assign)) {
            $_output .= "\$_smarty_tpl->assign('{$_assign}' , {$_function} (" . var_export($_attr, true) . ",\$_smarty_tpl), true);?>";
        } else {
            $_output .= "echo {$_function}(" . var_export($_attr, true) . ",\$_smarty_tpl);?>";
        }
        $_tpl = $_template;
        while ($_tpl->parent instanceof Smarty_Internal_Template) {
            $_tpl = $_tpl->parent;
        }

        return "" . $_output . "";
    }

}
