<?php




class Smarty_Internal_Compile_Extends extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('file');
    
    public $shorttag_order = array('file');

    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }
        if (strpos($_attr['file'], '$_tmp') !== false) {
            $compiler->trigger_template_error('illegal value for file attribute', $compiler->lex->taglineno);
        }

        $name = $_attr['file'];
        $_smarty_tpl = $compiler->template;
        eval("\$tpl_name = $name;");
        
        $_template = new $compiler->smarty->template_class($tpl_name, $compiler->smarty, $compiler->template);
        
        $uid = $_template->source->uid;
        if (isset($compiler->extends_uid[$uid])) {
            $compiler->trigger_template_error("illegal recursive call of \"$include_file\"", $this->lex->line - 1);
        }
        $compiler->extends_uid[$uid] = true;
        if (empty($_template->source->components)) {
            array_unshift($compiler->sources, $_template->source);
        } else {
            foreach ($_template->source->components as $source) {
                array_unshift($compiler->sources, $source);
                $uid = $source->uid;
                if (isset($compiler->extends_uid[$uid])) {
                    $compiler->trigger_template_error("illegal recursive call of \"{$sorce->filepath}\"", $this->lex->line - 1);
                }
                $compiler->extends_uid[$uid] = true;
            }
        }
        unset ($_template);
        $compiler->inheritance_child = true;
        $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBODY);
        return '';
    }
}
