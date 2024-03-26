<?php



abstract class Smarty_Internal_CompileBase
{
    
    public $required_attributes = array();
    
    public $optional_attributes = array();
    
    public $shorttag_order = array();
    
    public $option_flags = array('nocache');

    
    public function getAttributes($compiler, $attributes)
    {
        $_indexed_attr = array();
        
        foreach ($attributes as $key => $mixed) {
            
            if (!is_array($mixed)) {
                
                if (in_array(trim($mixed, '\'"'), $this->option_flags)) {
                    $_indexed_attr[trim($mixed, '\'"')] = true;
                    
                } elseif (isset($this->shorttag_order[$key])) {
                    $_indexed_attr[$this->shorttag_order[$key]] = $mixed;
                } else {
                    
                    $compiler->trigger_template_error('too many shorthand attributes', $compiler->lex->taglineno);
                }
                
            } else {
                foreach ($mixed as $k => $v) {
                    
                    if (in_array($k, $this->option_flags)) {
                        if (is_bool($v)) {
                            $_indexed_attr[$k] = $v;
                        } elseif (is_string($v) && in_array(trim($v, '\'"'), array('true', 'false'))) {
                            if (trim($v) == 'true') {
                                $_indexed_attr[$k] = true;
                            } else {
                                $_indexed_attr[$k] = false;
                            }
                        } elseif (is_numeric($v) && in_array($v, array(0, 1))) {
                            if ($v == 1) {
                                $_indexed_attr[$k] = true;
                            } else {
                                $_indexed_attr[$k] = false;
                            }
                        } else {
                            $compiler->trigger_template_error("illegal value of option flag \"{$k}\"", $compiler->lex->taglineno);
                        }
                        
                    } else {
                        reset($mixed);
                        $_indexed_attr[key($mixed)] = $mixed[key($mixed)];
                    }
                }
            }
        }
        
        foreach ($this->required_attributes as $attr) {
            if (!array_key_exists($attr, $_indexed_attr)) {
                $compiler->trigger_template_error("missing \"" . $attr . "\" attribute", $compiler->lex->taglineno);
            }
        }
        
        if ($this->optional_attributes != array('_any')) {
            $tmp_array = array_merge($this->required_attributes, $this->optional_attributes, $this->option_flags);
            foreach ($_indexed_attr as $key => $dummy) {
                if (!in_array($key, $tmp_array) && $key !== 0) {
                    $compiler->trigger_template_error("unexpected \"" . $key . "\" attribute", $compiler->lex->taglineno);
                }
            }
        }
        
        foreach ($this->option_flags as $flag) {
            if (!isset($_indexed_attr[$flag])) {
                $_indexed_attr[$flag] = false;
            }
        }

        return $_indexed_attr;
    }

    
    public function openTag($compiler, $openTag, $data = null)
    {
        array_push($compiler->_tag_stack, array($openTag, $data));
    }

    
    public function closeTag($compiler, $expectedTag)
    {
        if (count($compiler->_tag_stack) > 0) {
            
            list($_openTag, $_data) = array_pop($compiler->_tag_stack);
            
            if (in_array($_openTag, (array) $expectedTag)) {
                if (is_null($_data)) {
                    
                    return $_openTag;
                } else {
                    
                    return $_data;
                }
            }
            
            $compiler->trigger_template_error("unclosed {$compiler->smarty->left_delimiter}" . $_openTag . "{$compiler->smarty->right_delimiter} tag");

            return;
        }
        
        $compiler->trigger_template_error("unexpected closing tag", $compiler->lex->taglineno);

        return;
    }

}
