<?php



class Smarty_Internal_Compile_Setfilter extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler, $parameter)
    {
        $compiler->variable_filter_stack[] = $compiler->template->variable_filters;
        $compiler->template->variable_filters = $parameter['modifier_list'];
        
        $compiler->has_code = false;

        return true;
    }

}


class Smarty_Internal_Compile_Setfilterclose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        
        if (count($compiler->variable_filter_stack)) {
            $compiler->template->variable_filters = array_pop($compiler->variable_filter_stack);
        } else {
            $compiler->template->variable_filters = array();
        }
        
        $compiler->has_code = false;

        return true;
    }

}
