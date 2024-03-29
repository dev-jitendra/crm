<?php



class Smarty_Internal_Compile_Ldelim extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        
        $compiler->has_code = true;

        return $compiler->smarty->left_delimiter;
    }

}
