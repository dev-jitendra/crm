<?php




class Smarty_Internal_Compile_Block extends Smarty_Internal_CompileBase
{

    const parent = '____SMARTY_BLOCK_PARENT____';
    
    public $required_attributes = array('name');

    
    public $shorttag_order = array('name');

    
    public $option_flags = array('hide', 'append', 'prepend', 'nocache');

    
    public $optional_attributes = array('internal_file', 'internal_uid', 'internal_line');
    
    public static $nested_block_names = array();

    
    public static $block_data = array();

    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        $_name = trim($_attr['name'], "\"'");

        
        if ($compiler->inheritance_child) {
            array_unshift(self::$nested_block_names, $_name);
            $this->template->block_data[$_name]['source'] = '';
            
            self::$block_data[$_name]['source'] =
                "{$compiler->smarty->left_delimiter}private_child_block name={$_attr['name']} file='{$compiler->template->source->filepath}'" .
                " uid='{$compiler->template->source->uid}' line={$compiler->lex->line}";
            if ($_attr['nocache']) {
                self::$block_data[$_name]['source'] .= ' nocache';
            }
            self::$block_data[$_name]['source'] .= $compiler->smarty->right_delimiter;

            $save = array($_attr, $compiler->inheritance);
            $this->openTag($compiler, 'block', $save);
            
            $compiler->inheritance = true;
            $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBLOCK);
            $compiler->has_code = false;
            return;
        }
        
        if ($_attr['nocache'] == true) {
            $compiler->tag_nocache = true;
        }
        $save = array($_attr, $compiler->inheritance, $compiler->parser->current_buffer, $compiler->nocache);
        $this->openTag($compiler, 'block', $save);
        $compiler->inheritance = true;
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;

        $compiler->parser->current_buffer = new _smarty_template_buffer($compiler->parser);
        $compiler->has_code = false;

        return true;
    }


    
    static function compileChildBlock($compiler, $_name = null)
    {
        if ($compiler->inheritance_child) {
            $name1 = Smarty_Internal_Compile_Block::$nested_block_names[0];
            if (isset($compiler->template->block_data[$name1])) {
                
                Smarty_Internal_Compile_Block::$block_data[$name1]['source'] .= $compiler->template->block_data[$name1]['source'];
                Smarty_Internal_Compile_Block::$block_data[$name1]['child'] = true;
            }
            $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBLOCK);
            $compiler->has_code = false;
            return;
        }
        
        if ($_name == null) {
            $stack_count = count($compiler->_tag_stack);
            while (--$stack_count >= 0) {
                if ($compiler->_tag_stack[$stack_count][0] == 'block') {
                    $_name = trim($compiler->_tag_stack[$stack_count][1][0]['name'], "\"'");
                    break;
                }
            }
        }
        if ($_name == null) {
            $compiler->trigger_template_error(' tag {$smarty.block.child} used outside {block} tags ', $compiler->lex->taglineno);
        }
        
        if (!isset($compiler->template->block_data[$_name]['source'])) {
            $compiler->popTrace();
            return '';
        }
        
        $compiler->template->block_data[$_name]['compiled'] = true;
        $_tpl = new Smarty_Internal_template('string:' . $compiler->template->block_data[$_name]['source'], $compiler->smarty, $compiler->template, $compiler->template->cache_id,
            $compiler->template->compile_id, $compiler->template->caching, $compiler->template->cache_lifetime);
        if ($compiler->smarty->debugging) {
            Smarty_Internal_Debug::ignore($_tpl);
        }
        $_tpl->tpl_vars = $compiler->template->tpl_vars;
        $_tpl->variable_filters = $compiler->template->variable_filters;
        $_tpl->properties['nocache_hash'] = $compiler->template->properties['nocache_hash'];
        $_tpl->allow_relative_path = true;
        $_tpl->compiler->inheritance = true;
        $_tpl->compiler->suppressHeader = true;
        $_tpl->compiler->suppressFilter = true;
        $_tpl->compiler->suppressTemplatePropertyHeader = true;
        $_tpl->compiler->suppressMergedTemplates = true;
        $nocache = $compiler->nocache || $compiler->tag_nocache;
        if (strpos($compiler->template->block_data[$_name]['source'], self::parent) !== false) {
            $_output = str_replace(self::parent, $compiler->parser->current_buffer->to_smarty_php(), $_tpl->compiler->compileTemplate($_tpl, $nocache));
        } elseif ($compiler->template->block_data[$_name]['mode'] == 'prepend') {
            $_output = $_tpl->compiler->compileTemplate($_tpl, $nocache) . $compiler->parser->current_buffer->to_smarty_php();
        } elseif ($compiler->template->block_data[$_name]['mode'] == 'append') {
            $_output = $compiler->parser->current_buffer->to_smarty_php() . $_tpl->compiler->compileTemplate($_tpl, $nocache);
        } elseif (!empty($compiler->template->block_data[$_name])) {
            $_output = $_tpl->compiler->compileTemplate($_tpl, $nocache);
        }
        $compiler->template->properties['file_dependency'] = array_merge($compiler->template->properties['file_dependency'], $_tpl->properties['file_dependency']);
        $compiler->template->properties['function'] = array_merge($compiler->template->properties['function'], $_tpl->properties['function']);
        $compiler->merged_templates = array_merge($compiler->merged_templates, $_tpl->compiler->merged_templates);
        $compiler->template->variable_filters = $_tpl->variable_filters;
        if ($_tpl->has_nocache_code) {
            $compiler->template->has_nocache_code = true;
        }
        foreach ($_tpl->required_plugins as $key => $tmp1) {
            if ($compiler->nocache && $compiler->template->caching) {
                $code = 'nocache';
            } else {
                $code = $key;
            }
            foreach ($tmp1 as $name => $tmp) {
                foreach ($tmp as $type => $data) {
                    $compiler->template->required_plugins[$code][$name][$type] = $data;
                }
            }
        }
        unset($_tpl);
        $compiler->has_code = true;
        return $_output;
    }

    
    static function compileParentBlock($compiler, $_name = null)
    {
        
        if ($_name == null) {
            $stack_count = count($compiler->_tag_stack);
            while (--$stack_count >= 0) {
                if ($compiler->_tag_stack[$stack_count][0] == 'block') {
                    $_name = trim($compiler->_tag_stack[$stack_count][1][0]['name'], "\"'");
                    break;
                }
            }
        }
        if ($_name == null) {
            $compiler->trigger_template_error(' tag {$smarty.block.parent} used outside {block} tags ', $compiler->lex->taglineno);
        }
        if (empty(Smarty_Internal_Compile_Block::$nested_block_names)) {
            $compiler->trigger_template_error(' illegal {$smarty.block.parent} in parent template ', $compiler->lex->taglineno);
        }
        Smarty_Internal_Compile_Block::$block_data[Smarty_Internal_Compile_Block::$nested_block_names[0]]['source'] .= Smarty_Internal_Compile_Block::parent;
        $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBLOCK);
        $compiler->has_code = false;
        return;
    }

    
    static function blockSource($compiler, $source)
    {
        Smarty_Internal_Compile_Block::$block_data[Smarty_Internal_Compile_Block::$nested_block_names[0]]['source'] .= $source;
    }

}



class Smarty_Internal_Compile_Blockclose extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler)
    {
        $compiler->has_code = true;
        
        $_attr = $this->getAttributes($compiler, $args);
        $saved_data = $this->closeTag($compiler, array('block'));
        $_name = trim($saved_data[0]['name'], "\"'");
        
        $compiler->inheritance = $saved_data[1];
        
        if ($compiler->inheritance_child) {
            $name1 = Smarty_Internal_Compile_Block::$nested_block_names[0];
            Smarty_Internal_Compile_Block::$block_data[$name1]['source'] .= "{$compiler->smarty->left_delimiter}/private_child_block{$compiler->smarty->right_delimiter}";
            $level = count(Smarty_Internal_Compile_Block::$nested_block_names);
            array_shift(Smarty_Internal_Compile_Block::$nested_block_names);
            if (!empty(Smarty_Internal_Compile_Block::$nested_block_names)) {
                $name2 = Smarty_Internal_Compile_Block::$nested_block_names[0];
                if (isset($compiler->template->block_data[$name1]) || !$saved_data[0]['hide']) {
                    if (isset(Smarty_Internal_Compile_Block::$block_data[$name1]['child']) || !isset($compiler->template->block_data[$name1])) {
                        Smarty_Internal_Compile_Block::$block_data[$name2]['source'] .= Smarty_Internal_Compile_Block::$block_data[$name1]['source'];
                    } else {
                        if ($compiler->template->block_data[$name1]['mode'] == 'append') {
                            Smarty_Internal_Compile_Block::$block_data[$name2]['source'] .= Smarty_Internal_Compile_Block::$block_data[$name1]['source'] . $compiler->template->block_data[$name1]['source'];
                        } elseif ($compiler->template->block_data[$name1]['mode'] == 'prepend') {
                            Smarty_Internal_Compile_Block::$block_data[$name2]['source'] .= $compiler->template->block_data[$name1]['source'] . Smarty_Internal_Compile_Block::$block_data[$name1]['source'];
                        } else {
                            Smarty_Internal_Compile_Block::$block_data[$name2]['source'] .= $compiler->template->block_data[$name1]['source'];
                        }
                    }
                }
                unset(Smarty_Internal_Compile_Block::$block_data[$name1]);
                $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBLOCK);
            } else {
                if (isset($compiler->template->block_data[$name1]) || !$saved_data[0]['hide']) {
                    if (isset($compiler->template->block_data[$name1]) && !isset(Smarty_Internal_Compile_Block::$block_data[$name1]['child'])) {
                        if (strpos($compiler->template->block_data[$name1]['source'], Smarty_Internal_Compile_Block::parent) !== false) {
                            $compiler->template->block_data[$name1]['source'] =
                                str_replace(Smarty_Internal_Compile_Block::parent, Smarty_Internal_Compile_Block::$block_data[$name1]['source'], $compiler->template->block_data[$name1]['source']);
                        } elseif ($compiler->template->block_data[$name1]['mode'] == 'prepend') {
                            $compiler->template->block_data[$name1]['source'] .= Smarty_Internal_Compile_Block::$block_data[$name1]['source'];
                        } elseif ($compiler->template->block_data[$name1]['mode'] == 'append') {
                            $compiler->template->block_data[$name1]['source'] = Smarty_Internal_Compile_Block::$block_data[$name1]['source'] . $compiler->template->block_data[$name1]['source'];
                        }
                    } else {
                        $compiler->template->block_data[$name1]['source'] = Smarty_Internal_Compile_Block::$block_data[$name1]['source'];
                    }
                    $compiler->template->block_data[$name1]['mode'] = 'replace';
                    if ($saved_data[0]['append']) {
                        $compiler->template->block_data[$name1]['mode'] = 'append';
                    }
                    if ($saved_data[0]['prepend']) {
                        $compiler->template->block_data[$name1]['mode'] = 'prepend';
                    }
                }
                unset(Smarty_Internal_Compile_Block::$block_data[$name1]);
                $compiler->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBODY);
            }
            $compiler->has_code = false;
            return;
        }
        if (isset($compiler->template->block_data[$_name]) && !isset($compiler->template->block_data[$_name]['compiled'])) {
            $_output = Smarty_Internal_Compile_Block::compileChildBlock($compiler, $_name);
        } else {
            if ($saved_data[0]['hide'] && !isset($compiler->template->block_data[$_name]['source'])) {
                $_output = '';
            } else {
                $_output = $compiler->parser->current_buffer->to_smarty_php();
            }
        }
        unset($compiler->template->block_data[$_name]['compiled']);
        
        $compiler->parser->current_buffer = $saved_data[2];
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        $compiler->nocache = $saved_data[3];
        
        $compiler->suppressNocacheProcessing = true;

        return $_output;
    }
}


class Smarty_Internal_Compile_Private_Child_Block extends Smarty_Internal_CompileBase
{

    
    public $required_attributes = array('name', 'file', 'uid', 'line');


    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);

        
        if ($_attr['nocache'] == true) {
            $compiler->tag_nocache = true;
        }
        $save = array($_attr, $compiler->nocache);

        
        $compiler->pushTrace(trim($_attr['file'], "\"'"), trim($_attr['uid'], "\"'"), $_attr['line'] - $compiler->lex->line);

        $this->openTag($compiler, 'private_child_block', $save);

        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        $compiler->has_code = false;

        return true;
    }
}


class Smarty_Internal_Compile_Private_Child_Blockclose extends Smarty_Internal_CompileBase
{


    
    public function compile($args, $compiler)
    {
        
        $_attr = $this->getAttributes($compiler, $args);

        $saved_data = $this->closeTag($compiler, array('private_child_block'));

        
        $compiler->popTrace();

        $compiler->nocache = $saved_data[1];
        $compiler->has_code = false;

        return true;
    }
}
