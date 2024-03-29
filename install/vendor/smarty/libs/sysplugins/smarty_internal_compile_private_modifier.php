<?php




class Smarty_Internal_Compile_Private_Modifier extends Smarty_Internal_CompileBase
{
    
    public function compile($args, $compiler, $parameter)
    {
        
        $_attr = $this->getAttributes($compiler, $args);
        $output = $parameter['value'];
        
        foreach ($parameter['modifierlist'] as $single_modifier) {
            $modifier = $single_modifier[0];
            $single_modifier[0] = $output;
            $params = implode(',', $single_modifier);
            
            if (isset($compiler->known_modifier_type[$modifier])) {
                $modifier_types = array($compiler->known_modifier_type[$modifier]);
            } else {
                $modifier_types = array(1, 2, 3, 4, 5, 6);
            }
            foreach ($modifier_types as $type) {
                switch ($type) {
                    case 1:
                        
                        if (isset($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$modifier])) {
                            $function = $compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$modifier][0];
                            if (!is_array($function)) {
                                $output = "{$function}({$params})";
                            } else {
                                if (is_object($function[0])) {
                                    $output = '$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][\'' . $modifier . '\'][0][0]->' . $function[1] . '(' . $params . ')';
                                } else {
                                    $output = $function[0] . '::' . $function[1] . '(' . $params . ')';
                                }
                            }
                            $compiler->known_modifier_type[$modifier] = $type;
                            break 2;
                        }
                        break;
                    case 2:
                        
                        if (isset($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIERCOMPILER][$modifier][0])) {
                            $output = call_user_func($compiler->smarty->registered_plugins[Smarty::PLUGIN_MODIFIERCOMPILER][$modifier][0], $single_modifier, $compiler->smarty);
                            $compiler->known_modifier_type[$modifier] = $type;
                            break 2;
                        }
                        break;
                    case 3:
                        
                        if ($compiler->smarty->loadPlugin('smarty_modifiercompiler_' . $modifier)) {
                            
                            if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedModifier($modifier, $compiler)) {
                                $plugin = 'smarty_modifiercompiler_' . $modifier;
                                $output = $plugin($single_modifier, $compiler);
                            }
                            $compiler->known_modifier_type[$modifier] = $type;
                            break 2;
                        }
                        break;
                    case 4:
                        
                        if ($function = $compiler->getPlugin($modifier, Smarty::PLUGIN_MODIFIER)) {
                            
                            if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedModifier($modifier, $compiler)) {
                                $output = "{$function}({$params})";
                            }
                            $compiler->known_modifier_type[$modifier] = $type;
                            break 2;
                        }
                        break;
                    case 5:
                        
                        if (is_callable($modifier)) {
                            
                            if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedPhpModifier($modifier, $compiler)) {
                                $output = "{$modifier}({$params})";
                            }
                            $compiler->known_modifier_type[$modifier] = $type;
                            break 2;
                        }
                        break;
                    case 6:
                        
                        if (isset($compiler->default_handler_plugins[Smarty::PLUGIN_MODIFIER][$modifier]) || (is_callable($compiler->smarty->default_plugin_handler_func) && $compiler->getPluginFromDefaultHandler($modifier, Smarty::PLUGIN_MODIFIER))) {
                            $function = $compiler->default_handler_plugins[Smarty::PLUGIN_MODIFIER][$modifier][0];
                            
                            if (!is_object($compiler->smarty->security_policy) || $compiler->smarty->security_policy->isTrustedModifier($modifier, $compiler)) {
                                if (!is_array($function)) {
                                    $output = "{$function}({$params})";
                                } else {
                                    if (is_object($function[0])) {
                                        $output = '$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][\'' . $modifier . '\'][0][0]->' . $function[1] . '(' . $params . ')';
                                    } else {
                                        $output = $function[0] . '::' . $function[1] . '(' . $params . ')';
                                    }
                                }
                            }
                            if (isset($compiler->template->required_plugins['nocache'][$modifier][Smarty::PLUGIN_MODIFIER]['file']) || isset($compiler->template->required_plugins['compiled'][$modifier][Smarty::PLUGIN_MODIFIER]['file'])) {
                                
                                $compiler->known_modifier_type[$modifier] = 4;
                            } else {
                                $compiler->known_modifier_type[$modifier] = $type;
                            }
                            break 2;
                        }
                }
            }
            if (!isset($compiler->known_modifier_type[$modifier])) {
                $compiler->trigger_template_error("unknown modifier \"" . $modifier . "\"", $compiler->lex->taglineno);
            }
        }

        return $output;
    }

}
