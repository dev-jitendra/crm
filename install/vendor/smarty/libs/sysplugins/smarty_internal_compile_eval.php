<?php



class Smarty_Internal_Compile_Eval extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('var');
    
    public $optional_attributes = array('assign');
    
    public $shorttag_order = array('var','assign');

    
    public function compile($args, $compiler)
    {
        $this->required_attributes = array('var');
        $this->optional_attributes = array('assign');
        
        $_attr = $this->getAttributes($compiler, $args);
        if (isset($_attr['assign'])) {
              
            $_assign = $_attr['assign'];
        }

        
        $_output = "\$_template = new {$compiler->smarty->template_class}('eval:'.".$_attr['var'].", \$_smarty_tpl->smarty, \$_smarty_tpl);";
        
        if (isset($_assign)) {
            $_output .= "\$_smarty_tpl->assign($_assign,\$_template->fetch());";
        } else {
            $_output .= "echo \$_template->fetch();";
        }

        return "<?php $_output ?>";
    }

}
