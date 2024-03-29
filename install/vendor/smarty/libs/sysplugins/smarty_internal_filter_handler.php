<?php



class Smarty_Internal_Filter_Handler
{
    
    public static function runFilter($type, $content, Smarty_Internal_Template $template)
    {
        $output = $content;
        
        if (!empty($template->smarty->autoload_filters[$type])) {
            foreach ((array) $template->smarty->autoload_filters[$type] as $name) {
                $plugin_name = "Smarty_{$type}filter_{$name}";
                if ($template->smarty->loadPlugin($plugin_name)) {
                    if (function_exists($plugin_name)) {
                        
                        $output = $plugin_name($output, $template);
                    } elseif (class_exists($plugin_name, false)) {
                        
                        $output = call_user_func(array($plugin_name, 'execute'), $output, $template);
                    }
                } else {
                    
                    throw new SmartyException("Unable to load filter {$plugin_name}");
                }
            }
        }
        
        if (!empty($template->smarty->registered_filters[$type])) {
            foreach ($template->smarty->registered_filters[$type] as $key => $name) {
                if (is_array($template->smarty->registered_filters[$type][$key])) {
                    $output = call_user_func($template->smarty->registered_filters[$type][$key], $output, $template);
                } else {
                    $output = $template->smarty->registered_filters[$type][$key]($output, $template);
                }
            }
        }
        
        return $output;
    }

}
