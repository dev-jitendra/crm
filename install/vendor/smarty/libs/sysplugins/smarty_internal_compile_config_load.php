<?php



class Smarty_Internal_Compile_Config_Load extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('file');
    
    public $shorttag_order = array('file','section');
    
    public $optional_attributes = array('section', 'scope');

    
    public function compile($args, $compiler)
    {
        static $_is_legal_scope = array('local' => true,'parent' => true,'root' => true,'global' => true);
        
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }

        
        $conf_file = $_attr['file'];
        if (isset($_attr['section'])) {
            $section = $_attr['section'];
        } else {
            $section = 'null';
        }
        $scope = 'local';
        
        if (isset($_attr['scope'])) {
            $_attr['scope'] = trim($_attr['scope'], "'\"");
            if (isset($_is_legal_scope[$_attr['scope']])) {
                $scope = $_attr['scope'];
           } else {
                $compiler->trigger_template_error('illegal value for "scope" attribute', $compiler->lex->taglineno);
           }
        }
        
        $_output = "<?php  \$_config = new Smarty_Internal_Config($conf_file, \$_smarty_tpl->smarty, \$_smarty_tpl);";
        $_output .= "\$_config->loadConfigVars($section, '$scope'); ?>";

        return $_output;
    }

}
