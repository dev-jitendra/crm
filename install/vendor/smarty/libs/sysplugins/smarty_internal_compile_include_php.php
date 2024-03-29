<?php



class Smarty_Internal_Compile_Include_Php extends Smarty_Internal_CompileBase
{
    
    public $required_attributes = array('file');
    
    public $shorttag_order = array('file');
    
    public $optional_attributes = array('once', 'assign');

    
    public function compile($args, $compiler)
    {
        if (!($compiler->smarty instanceof SmartyBC)) {
            throw new SmartyException("{include_php} is deprecated, use SmartyBC class to enable");
        }
        
        $_attr = $this->getAttributes($compiler, $args);

        $_output = '<?php ';

        $_smarty_tpl = $compiler->template;
        $_filepath = false;
        eval('$_file = ' . $_attr['file'] . ';');
        if (!isset($compiler->smarty->security_policy) && file_exists($_file)) {
            $_filepath = $_file;
        } else {
            if (isset($compiler->smarty->security_policy)) {
                $_dir = $compiler->smarty->security_policy->trusted_dir;
            } else {
                $_dir = $compiler->smarty->trusted_dir;
            }
            if (!empty($_dir)) {
                foreach ((array) $_dir as $_script_dir) {
                    $_script_dir = rtrim($_script_dir, '/\\') . DS;
                    if (file_exists($_script_dir . $_file)) {
                        $_filepath = $_script_dir .  $_file;
                        break;
                    }
                }
            }
        }
        if ($_filepath == false) {
            $compiler->trigger_template_error("{include_php} file '{$_file}' is not readable", $compiler->lex->taglineno);
        }

        if (isset($compiler->smarty->security_policy)) {
            $compiler->smarty->security_policy->isTrustedPHPDir($_filepath);
        }

        if (isset($_attr['assign'])) {
            
            $_assign = $_attr['assign'];
        }
        $_once = '_once';
        if (isset($_attr['once'])) {
            if ($_attr['once'] == 'false') {
                $_once = '';
            }
        }

        if (isset($_assign)) {
            return "<?php ob_start(); include{$_once} ('{$_filepath}'); \$_smarty_tpl->assign({$_assign},ob_get_contents()); ob_end_clean();?>";
        } else {
            return "<?php include{$_once} ('{$_filepath}');?>\n";
        }
    }

}
