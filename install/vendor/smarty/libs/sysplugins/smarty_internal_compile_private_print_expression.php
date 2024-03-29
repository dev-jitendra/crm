<?php



class Smarty_Internal_Compile_Private_Print_Expression extends Smarty_Internal_CompileBase
{
    
    public $optional_attributes = array('assign');
    
    public $option_flags = array('nocache', 'nofilter');

    
    public function compile($args, $compiler, $parameter)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        
        if ($_attr['nocache'] === true) {
            $compiler->tag_nocache = true;
        }
        
        if ($_attr['nofilter'] === true) {
            $_filter = 'false';
        } else {
            $_filter = 'true';
        }
        if (isset($_attr['assign'])) {
            
            $output = "<?php \$_smarty_tpl->assign({$_attr['assign']},{$parameter['value']});?>";
        } else {
            
            $output = $parameter['value'];
            
            if (!empty($parameter['modifierlist'])) {
                $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => $parameter['modifierlist'], 'value' => $output));
            }
            if (!$_attr['nofilter']) {
                
                if (!empty($compiler->smarty->default_modifiers)) {
                    if (empty($compiler->default_modifier_list)) {
                        $modifierlist = array();
                        foreach ($compiler->smarty->default_modifiers as $key => $single_default_modifier) {
                            preg_match_all('/(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|:|[^:]+)/', $single_default_modifier, $mod_array);
                            for ($i = 0, $count = count($mod_array[0]);$i < $count;$i++) {
                                if ($mod_array[0][$i] != ':') {
                                    $modifierlist[$key][] = $mod_array[0][$i];
                                }
                            }
                        }
                        $compiler->default_modifier_list  = $modifierlist;
                    }
                    $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => $compiler->default_modifier_list, 'value' => $output));
                }
                
                if ($compiler->template->smarty->escape_html) {
                    $output = "htmlspecialchars({$output}, ENT_QUOTES, '" . addslashes(Smarty::$_CHARSET) . "')";
                }
                
                if (!empty($compiler->template->smarty->registered_filters[Smarty::FILTER_VARIABLE])) {
                    foreach ($compiler->template->smarty->registered_filters[Smarty::FILTER_VARIABLE] as $key => $function) {
                        if (!is_array($function)) {
                            $output = "{$function}({$output},\$_smarty_tpl)";
                        } elseif (is_object($function[0])) {
                            $output = "\$_smarty_tpl->smarty->registered_filters[Smarty::FILTER_VARIABLE]['{$key}'][0]->{$function[1]}({$output},\$_smarty_tpl)";
                        } else {
                            $output = "{$function[0]}::{$function[1]}({$output},\$_smarty_tpl)";
                        }
                    }
                }
                
                if (isset($compiler->smarty->autoload_filters[Smarty::FILTER_VARIABLE])) {
                    foreach ((array) $compiler->template->smarty->autoload_filters[Smarty::FILTER_VARIABLE] as $name) {
                        $result = $this->compile_output_filter($compiler, $name, $output);
                        if ($result !== false) {
                            $output = $result;
                        } else {
                            
                            throw new SmartyException("Unable to load filter '{$name}'");
                        }
                    }
                }
                if (isset($compiler->template->variable_filters)) {
                    foreach ($compiler->template->variable_filters as $filter) {
                        if (count($filter) == 1 && ($result = $this->compile_output_filter($compiler, $filter[0], $output)) !== false) {
                            $output = $result;
                        } else {
                            $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => array($filter), 'value' => $output));
                        }
                    }
                }
            }

            $compiler->has_output = true;
            $output = "<?php echo {$output};?>";
        }

        return $output;
    }

    
    private function compile_output_filter($compiler, $name, $output)
    {
        $plugin_name = "smarty_variablefilter_{$name}";
        $path = $compiler->smarty->loadPlugin($plugin_name, false);
        if ($path) {
            if ($compiler->template->caching) {
                $compiler->template->required_plugins['nocache'][$name][Smarty::FILTER_VARIABLE]['file'] = $path;
                $compiler->template->required_plugins['nocache'][$name][Smarty::FILTER_VARIABLE]['function'] = $plugin_name;
            } else {
                $compiler->template->required_plugins['compiled'][$name][Smarty::FILTER_VARIABLE]['file'] = $path;
                $compiler->template->required_plugins['compiled'][$name][Smarty::FILTER_VARIABLE]['function'] = $plugin_name;
            }
        } else {
            
            return false;
        }

        return "{$plugin_name}({$output},\$_smarty_tpl)";
    }

}
