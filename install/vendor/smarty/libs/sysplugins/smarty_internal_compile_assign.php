<?php



class Smarty_Internal_Compile_Assign extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler, $parameter)
    {
        
        $this->required_attributes = array('var', 'value');
        $this->shorttag_order = array('var', 'value');
        $this->optional_attributes = array('scope');
        $_nocache = 'null';
        $_scope = Smarty::SCOPE_LOCAL;
        
        $_attr = $this->getAttributes($compiler, $args);
        
        if ($compiler->tag_nocache || $compiler->nocache) {
            $_nocache = 'true';
            
            if (isset($compiler->template->tpl_vars[trim($_attr['var'], "'")])) {
                $compiler->template->tpl_vars[trim($_attr['var'], "'")]->nocache = true;
            } else {
                $compiler->template->tpl_vars[trim($_attr['var'], "'")] = new Smarty_variable(null, true);
            }
        }
        
        if (isset($_attr['scope'])) {
            $_attr['scope'] = trim($_attr['scope'], "'\"");
            if ($_attr['scope'] == 'parent') {
                $_scope = Smarty::SCOPE_PARENT;
            } elseif ($_attr['scope'] == 'root') {
                $_scope = Smarty::SCOPE_ROOT;
            } elseif ($_attr['scope'] == 'global') {
                $_scope = Smarty::SCOPE_GLOBAL;
            } else {
                $compiler->trigger_template_error('illegal value for "scope" attribute', $compiler->lex->taglineno);
            }
        }
        
        if (isset($parameter['smarty_internal_index'])) {
            $output = "<?php \$_smarty_tpl->createLocalArrayVariable($_attr[var], $_nocache, $_scope);\n\$_smarty_tpl->tpl_vars[$_attr[var]]->value$parameter[smarty_internal_index] = $_attr[value];";
        } else {
            
            if ($compiler->template->smarty instanceof SmartyBC) {
                $output = "<?php if (isset(\$_smarty_tpl->tpl_vars[$_attr[var]])) {\$_smarty_tpl->tpl_vars[$_attr[var]] = clone \$_smarty_tpl->tpl_vars[$_attr[var]];";
                $output .= "\n\$_smarty_tpl->tpl_vars[$_attr[var]]->value = $_attr[value]; \$_smarty_tpl->tpl_vars[$_attr[var]]->nocache = $_nocache; \$_smarty_tpl->tpl_vars[$_attr[var]]->scope = $_scope;";
                $output .= "\n} else \$_smarty_tpl->tpl_vars[$_attr[var]] = new Smarty_variable($_attr[value], $_nocache, $_scope);";
            } else {
                $output = "<?php \$_smarty_tpl->tpl_vars[$_attr[var]] = new Smarty_variable($_attr[value], $_nocache, $_scope);";
            }
        }
        if ($_scope == Smarty::SCOPE_PARENT) {
            $output .= "\nif (\$_smarty_tpl->parent != null) \$_smarty_tpl->parent->tpl_vars[$_attr[var]] = clone \$_smarty_tpl->tpl_vars[$_attr[var]];";
        } elseif ($_scope == Smarty::SCOPE_ROOT || $_scope == Smarty::SCOPE_GLOBAL) {
            $output .= "\n\$_ptr = \$_smarty_tpl->parent; while (\$_ptr != null) {\$_ptr->tpl_vars[$_attr[var]] = clone \$_smarty_tpl->tpl_vars[$_attr[var]]; \$_ptr = \$_ptr->parent; }";
        }
        if ($_scope == Smarty::SCOPE_GLOBAL) {
            $output .= "\nSmarty::\$global_tpl_vars[$_attr[var]] = clone \$_smarty_tpl->tpl_vars[$_attr[var]];";
        }
        $output .= '?>';

        return $output;
    }

}
