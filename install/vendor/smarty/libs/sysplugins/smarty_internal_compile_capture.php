<?php



class Smarty_Internal_Compile_Capture extends Smarty_Internal_CompileBase
{
    
    public $shorttag_order = array('name');
    
    public $optional_attributes = array('name', 'assign', 'append');

    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);

        $buffer = isset($_attr['name']) ? $_attr['name'] : "'default'";
        $assign = isset($_attr['assign']) ? $_attr['assign'] : 'null';
        $append = isset($_attr['append']) ? $_attr['append'] : 'null';

        $compiler->_capture_stack[0][] = array($buffer, $assign, $append, $compiler->nocache);
        
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        $_output = "<?php \$_smarty_tpl->_capture_stack[0][] = array($buffer, $assign, $append); ob_start(); ?>";

        return $_output;
    }

}


class Smarty_Internal_Compile_CaptureClose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($buffer, $assign, $append, $compiler->nocache) = array_pop($compiler->_capture_stack[0]);

        $_output = "<?php list(\$_capture_buffer, \$_capture_assign, \$_capture_append) = array_pop(\$_smarty_tpl->_capture_stack[0]);\n";
        $_output .= "if (!empty(\$_capture_buffer)) {\n";
        $_output .= " if (isset(\$_capture_assign)) \$_smarty_tpl->assign(\$_capture_assign, ob_get_contents());\n";
        $_output .= " if (isset( \$_capture_append)) \$_smarty_tpl->append( \$_capture_append, ob_get_contents());\n";
        $_output .= " Smarty::\$_smarty_vars['capture'][\$_capture_buffer]=ob_get_clean();\n";
        $_output .= "} else \$_smarty_tpl->capture_error();?>";

        return $_output;
    }

}
