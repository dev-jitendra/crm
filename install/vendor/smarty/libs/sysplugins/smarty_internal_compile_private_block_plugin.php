<?php



class Smarty_Internal_Compile_Private_Block_Plugin extends Smarty_Internal_CompileBase
{
    
    public $optional_attributes = array('_any');

    
    public function compile($args, $compiler, $parameter, $tag, $function)
    {
        if (!isset($tag[5]) || substr($tag, -5) != 'close') {
            
            
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

            $this->openTag($compiler, $tag, array($_params, $compiler->nocache));
            
            $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
            
            $output = "<?php \$_smarty_tpl->smarty->_tag_stack[] = array('{$tag}', {$_params}); \$_block_repeat=true; echo {$function}({$_params}, null, \$_smarty_tpl, \$_block_repeat);while (\$_block_repeat) { ob_start();?>";
        } else {
            
            if ($compiler->nocache) {
                $compiler->tag_nocache = true;
            }
            
            list($_params, $compiler->nocache) = $this->closeTag($compiler, substr($tag, 0, -5));
            
            $compiler->has_output = true;
            
            if (!isset($parameter['modifier_list'])) {
                $mod_pre = $mod_post ='';
            } else {
                $mod_pre = ' ob_start(); ';
                $mod_post = 'echo '.$compiler->compileTag('private_modifier',array(),array('modifierlist'=>$parameter['modifier_list'],'value'=>'ob_get_clean()')).';';
            }
            $output = "<?php \$_block_content = ob_get_clean(); \$_block_repeat=false;".$mod_pre." echo {$function}({$_params}, \$_block_content, \$_smarty_tpl, \$_block_repeat); ".$mod_post." } array_pop(\$_smarty_tpl->smarty->_tag_stack);?>";
        }

        return $output . "\n";
    }

}
