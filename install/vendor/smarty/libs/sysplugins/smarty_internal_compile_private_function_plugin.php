<?php



class Smarty_Internal_Compile_Private_Function_Plugin extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array();
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler, $parameter, $tag, $function)
    {
        
        $compiler->has_output = true;

        
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->tag_nocache = true;
        }
        unset($_attr['nocache']);
        
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = "$_key=>$_value";
            } else {
                $_paramsArray[] = "'$_key'=>$_value";
            }
        }
        $_params = 'array(' . implode(",", $_paramsArray) . ')';
        
        $output = "<?php echo {$function}({$_params},\$_smarty_tpl);?>\n";

        return $output;
    }

}
