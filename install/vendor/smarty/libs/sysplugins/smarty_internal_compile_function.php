<?php



class Smarty_Internal_Compile_Function extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('name');
    
    public $shorttag_order = array('name');
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler, $parameter)
    {
        
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        unset($_attr['nocache']);
        $save = array($_attr, $compiler->parser->current_buffer,
            $compiler->template->has_nocache_code, $compiler->template->required_plugins);
        $this->openTag($compiler, 'function', $save);
        $_name = trim($_attr['name'], "'\"");
        unset($_attr['name']);
        
        $compiler->compiles_template_function = true;
        $compiler->template->properties['function'][$_name]['parameter'] = array();
        $_smarty_tpl = $compiler->template;
        foreach ($_attr as $_key => $_data) {
            eval ('$tmp='.$_data.';');
            $compiler->template->properties['function'][$_name]['parameter'][$_key] = $tmp;
        }
        $compiler->smarty->template_functions[$_name]['parameter'] = $compiler->template->properties['function'][$_name]['parameter'];
        if ($compiler->template->caching) {
            $output = '';
        } else {
            $output = "<?php if (!function_exists('smarty_template_function_{$_name}')) {
    function smarty_template_function_{$_name}(\$_smarty_tpl,\$params) {
    \$saved_tpl_vars = \$_smarty_tpl->tpl_vars;
    foreach (\$_smarty_tpl->smarty->template_functions['{$_name}']['parameter'] as \$key => \$value) {\$_smarty_tpl->tpl_vars[\$key] = new Smarty_variable(\$value);};
    foreach (\$params as \$key => \$value) {\$_smarty_tpl->tpl_vars[\$key] = new Smarty_variable(\$value);}?>";
        }
        
        $compiler->template->required_plugins = array('compiled' => array(), 'nocache' => array());
        $compiler->parser->current_buffer = new _smarty_template_buffer($compiler->parser);
        $compiler->parser->current_buffer->append_subtree(new _smarty_tag($compiler->parser, $output));
        $compiler->template->has_nocache_code = false;
        $compiler->has_code = false;
        $compiler->template->properties['function'][$_name]['compiled'] = '';
        return true;
    }

}


class Smarty_Internal_Compile_Functionclose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler, $parameter)
    {
        $_attr = $this->getAttributes($compiler, $args);
        $saved_data = $this->closeTag($compiler, array('function'));
        $_name = trim($saved_data[0]['name'], "'\"");
        
        $plugins_string = '';
        if (!empty($compiler->template->required_plugins['compiled'])) {
            $plugins_string = '<?php ';
            foreach ($compiler->template->required_plugins['compiled'] as $tmp) {
                foreach ($tmp as $data) {
                    $plugins_string .= "if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";
                }
            }
            $plugins_string .= '?>';
        }
        if (!empty($compiler->template->required_plugins['nocache'])) {
            $plugins_string .= "<?php echo '<?php ";
            foreach ($compiler->template->required_plugins['nocache'] as $tmp) {
                foreach ($tmp as $data) {
                    $plugins_string .= "if (!is_callable(\'{$data['function']}\')) include \'{$data['file']}\';\n";
                }
            }
            $plugins_string .= "?>';?>\n";
        }
         
         $last = count($compiler->parser->current_buffer->subtrees) - 1;
         if ($compiler->parser->current_buffer->subtrees[$last] instanceof _smarty_linebreak) {
             unset($compiler->parser->current_buffer->subtrees[$last]);
         }
        
        if ($compiler->template->caching) {
            $compiler->template->properties['function'][$_name]['compiled'] .= $plugins_string
             . $compiler->parser->current_buffer->to_smarty_php();
            $compiler->template->properties['function'][$_name]['nocache_hash'] = $compiler->template->properties['nocache_hash'];
            $compiler->template->properties['function'][$_name]['has_nocache_code'] = $compiler->template->has_nocache_code;
            $compiler->template->properties['function'][$_name]['called_functions'] = $compiler->called_functions;
            $compiler->called_functions = array();
            $compiler->smarty->template_functions[$_name] = $compiler->template->properties['function'][$_name];
            $compiler->has_code = false;
            $output = true;
        } else {
            $output = $plugins_string . $compiler->parser->current_buffer->to_smarty_php() . "<?php \$_smarty_tpl->tpl_vars = \$saved_tpl_vars;
foreach (Smarty::\$global_tpl_vars as \$key => \$value) if(!isset(\$_smarty_tpl->tpl_vars[\$key])) \$_smarty_tpl->tpl_vars[\$key] = \$value;}}?>\n";
        }
        
        $compiler->compiles_template_function = false;
        
        $compiler->parser->current_buffer = $saved_data[1];
        $compiler->template->has_nocache_code = $compiler->template->has_nocache_code | $saved_data[2];
        $compiler->template->required_plugins = $saved_data[3];

        return $output;
    }

}
