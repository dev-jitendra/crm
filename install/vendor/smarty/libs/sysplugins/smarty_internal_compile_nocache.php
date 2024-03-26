<?php



class Smarty_Internal_Compile_Nocache extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        if ($compiler->template->caching) {
        
        $this->openTag($compiler, 'nocache', $compiler->nocache);
        $compiler->nocache = true;
        }
        
        $compiler->has_code = false;

        return true;
    }

}


class Smarty_Internal_Compile_Nocacheclose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->template->caching) {
        
        $compiler->nocache = $this->closeTag($compiler, 'nocache');
        }
        
        $compiler->has_code = false;

        return true;
    }

}
