<?php



class Smarty_Internal_Compile_While extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler, $parameter)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        $this->openTag($compiler, 'while', $compiler->nocache);

        if (!array_key_exists("if condition",$parameter)) {
            $compiler->trigger_template_error("missing while condition", $compiler->lex->taglineno);
        }

        
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        if (is_array($parameter['if condition'])) {
            if ($compiler->nocache) {
                $_nocache = ',true';
                
                if (is_array($parameter['if condition']['var'])) {
                    $compiler->template->tpl_vars[trim($parameter['if condition']['var']['var'], "'")] = new Smarty_variable(null, true);
                } else {
                    $compiler->template->tpl_vars[trim($parameter['if condition']['var'], "'")] = new Smarty_variable(null, true);
                }
            } else {
                $_nocache = '';
            }
            if (is_array($parameter['if condition']['var'])) {
                $_output = "<?php if (!isset(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] . "]) || !is_array(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] . "]->value)) \$_smarty_tpl->createLocalArrayVariable(" . $parameter['if condition']['var']['var'] . "$_nocache);\n";
                $_output .= "while (\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] . "]->value" . $parameter['if condition']['var']['smarty_internal_index'] . " = " . $parameter['if condition']['value'] . ") {?>";
            } else {
                $_output = "<?php if (!isset(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] . "])) \$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] . "] = new Smarty_Variable(null{$_nocache});";
                $_output .= "while (\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] . "]->value = " . $parameter['if condition']['value'] . ") {?>";
            }

            return $_output;
        } else {
            return "<?php while ({$parameter['if condition']}) {?>";
        }
    }

}


class Smarty_Internal_Compile_Whileclose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        $compiler->nocache = $this->closeTag($compiler, array('while'));

        return "<?php }?>";
    }

}
