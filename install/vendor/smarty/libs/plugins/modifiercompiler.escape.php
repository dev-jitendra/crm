<?php



require_once( SMARTY_PLUGINS_DIR .'shared.literal_compiler_param.php' );


function smarty_modifiercompiler_escape($params, $compiler)
{
    static $_double_encode = null;
    if ($_double_encode === null) {
        $_double_encode = version_compare(PHP_VERSION, '5.2.3', '>=');
    }

    try {
        $esc_type = smarty_literal_compiler_param($params, 1, 'html');
        $char_set = smarty_literal_compiler_param($params, 2, Smarty::$_CHARSET);
        $double_encode = smarty_literal_compiler_param($params, 3, true);

        if (!$char_set) {
            $char_set = Smarty::$_CHARSET;
        }

        switch ($esc_type) {
            case 'html':
                if ($_double_encode) {
                    return 'htmlspecialchars('
                        . $params[0] .', ENT_QUOTES, '
                        . var_export($char_set, true) . ', '
                        . var_export($double_encode, true) . ')';
                } elseif ($double_encode) {
                    return 'htmlspecialchars('
                        . $params[0] .', ENT_QUOTES, '
                        . var_export($char_set, true) . ')';
                } else {
                    
                }

            case 'htmlall':
                if (Smarty::$_MBSTRING) {
                    if ($_double_encode) {
                        
                        return 'mb_convert_encoding(htmlspecialchars('
                            . $params[0] .', ENT_QUOTES, '
                            . var_export($char_set, true) . ', '
                            . var_export($double_encode, true)
                            . '), "HTML-ENTITIES", '
                            . var_export($char_set, true) . ')';
                    } elseif ($double_encode) {
                        
                        return 'mb_convert_encoding(htmlspecialchars('
                            . $params[0] .', ENT_QUOTES, '
                            . var_export($char_set, true)
                            . '), "HTML-ENTITIES", '
                            . var_export($char_set, true) . ')';
                    } else {
                        
                    }
                }

                
                if ($_double_encode) {
                    
                    return 'htmlentities('
                        . $params[0] .', ENT_QUOTES, '
                        . var_export($char_set, true) . ', '
                        . var_export($double_encode, true) . ')';
                } elseif ($double_encode) {
                    
                    return 'htmlentities('
                        . $params[0] .', ENT_QUOTES, '
                        . var_export($char_set, true) . ')';
                } else {
                    
                }

            case 'url':
                return 'rawurlencode(' . $params[0] . ')';

            case 'urlpathinfo':
                return 'str_replace("%2F", "/", rawurlencode(' . $params[0] . '))';

            case 'quotes':
                
                return 'preg_replace("%(?<!\\\\\\\\)\'%", "\\\'",' . $params[0] . ')';

            case 'javascript':
                
                return 'strtr(' . $params[0] . ', array("\\\\" => "\\\\\\\\", "\'" => "\\\\\'", "\"" => "\\\\\"", "\\r" => "\\\\r", "\\n" => "\\\n", "</" => "<\/" ))';

        }
    } catch (SmartyException $e) {
        
    }

    
    if ($compiler->template->caching && ($compiler->tag_nocache | $compiler->nocache)) {
        $compiler->template->required_plugins['nocache']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.escape.php';
        $compiler->template->required_plugins['nocache']['escape']['modifier']['function'] = 'smarty_modifier_escape';
    } else {
        $compiler->template->required_plugins['compiled']['escape']['modifier']['file'] = SMARTY_PLUGINS_DIR .'modifier.escape.php';
        $compiler->template->required_plugins['compiled']['escape']['modifier']['function'] = 'smarty_modifier_escape';
    }

    return 'smarty_modifier_escape(' . join( ', ', $params ) . ')';
}
