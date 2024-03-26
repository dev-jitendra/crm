<?php



class Smarty_Internal_Compile_Debug extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);

        
        $compiler->tag_nocache = true;

        
        $_output = "<?php \$_smarty_tpl->smarty->loadPlugin('Smarty_Internal_Debug'); Smarty_Internal_Debug::display_debug(\$_smarty_tpl); ?>";

        return $_output;
    }

}
