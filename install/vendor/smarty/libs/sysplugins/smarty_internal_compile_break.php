<?php


class Smarty_Internal_Compile_Break extends Smarty_Internal_CompileBase
{
    
    public $optional_attributes = array('levels');
    
    public $shorttag_order = array('levels');

    
    public function compile($args, $compiler, $parameter)
    {
        static $_is_loopy = array('for' => true, 'foreach' => true, 'while' => true, 'section' => true);
        
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }

        if (isset($_attr['levels'])) {
            if (!is_numeric($_attr['levels'])) {
                $compiler->trigger_template_error('level attribute must be a numeric constant', $compiler->lex->taglineno);
            }
            $_levels = $_attr['levels'];
        } else {
            $_levels = 1;
        }
        $level_count = $_levels;
        $stack_count = count($compiler->_tag_stack) - 1;
        while ($level_count > 0 && $stack_count >= 0) {
            if (isset($_is_loopy[$compiler->_tag_stack[$stack_count][0]])) {
                $level_count--;
            }
            $stack_count--;
        }
        if ($level_count != 0) {
            $compiler->trigger_template_error("cannot break {$_levels} level(s)", $compiler->lex->taglineno);
        }

        return "<?php break {$_levels}?>";
    }

}
