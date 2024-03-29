<?php



function smarty_modifier_escape($string, $esc_type = 'html', $char_set = null, $double_encode = true)
{
    static $_double_encode = null;
    if ($_double_encode === null) {
        $_double_encode = version_compare(PHP_VERSION, '5.2.3', '>=');
    }

    if (!$char_set) {
        $char_set = Smarty::$_CHARSET;
    }

    switch ($esc_type) {
        case 'html':
            if ($_double_encode) {
                
                return htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
            } else {
                if ($double_encode) {
                    
                    return htmlspecialchars($string, ENT_QUOTES, $char_set);
                } else {
                    
                    $string = preg_replace('!&(#?\w+);!', '%%%SMARTY_START%%%\\1%%%SMARTY_END%%%', $string);
                    $string = htmlspecialchars($string, ENT_QUOTES, $char_set);
                    $string = str_replace(array('%%%SMARTY_START%%%', '%%%SMARTY_END%%%'), array('&', ';'), $string);

                    return $string;
                }
            }

        case 'htmlall':
            if (Smarty::$_MBSTRING) {
                
                if ($_double_encode) {
                    
                    $string = htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
                } else {
                    if ($double_encode) {
                        
                        $string = htmlspecialchars($string, ENT_QUOTES, $char_set);
                    } else {
                        
                        $string = preg_replace('!&(#?\w+);!', '%%%SMARTY_START%%%\\1%%%SMARTY_END%%%', $string);
                        $string = htmlspecialchars($string, ENT_QUOTES, $char_set);
                        $string = str_replace(array('%%%SMARTY_START%%%', '%%%SMARTY_END%%%'), array('&', ';'), $string);

                        return $string;
                    }
                }

                
                return mb_convert_encoding($string, 'HTML-ENTITIES', $char_set);
            }

            
            if ($_double_encode) {
                return htmlentities($string, ENT_QUOTES, $char_set, $double_encode);
            } else {
                if ($double_encode) {
                    return htmlentities($string, ENT_QUOTES, $char_set);
                } else {
                    $string = preg_replace('!&(#?\w+);!', '%%%SMARTY_START%%%\\1%%%SMARTY_END%%%', $string);
                    $string = htmlentities($string, ENT_QUOTES, $char_set);
                    $string = str_replace(array('%%%SMARTY_START%%%', '%%%SMARTY_END%%%'), array('&', ';'), $string);

                    return $string;
                }
            }

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F', '/', rawurlencode($string));

        case 'quotes':
            
            return preg_replace("%(?<!\\\\)'%", "\\'", $string);

        case 'hex':
            
            
            $return = '';
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '%' . bin2hex($string[$x]);
            }

            return $return;

        case 'hexentity':
            $return = '';
            if (Smarty::$_MBSTRING) {
                require_once(SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php');
                $return = '';
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#x' . strtoupper(dechex($unicode)) . ';';
                }

                return $return;
            }
            
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '&#x' . bin2hex($string[$x]) . ';';
            }

            return $return;

        case 'decentity':
            $return = '';
            if (Smarty::$_MBSTRING) {
                require_once(SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php');
                $return = '';
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#' . $unicode . ';';
                }

                return $return;
            }
            
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '&#' . ord($string[$x]) . ';';
            }

            return $return;

        case 'javascript':
            
            return strtr($string, array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));

        case 'mail':
            if (Smarty::$_MBSTRING) {
                require_once(SMARTY_PLUGINS_DIR . 'shared.mb_str_replace.php');

                return smarty_mb_str_replace(array('@', '.'), array(' [AT] ', ' [DOT] '), $string);
            }
            
            return str_replace(array('@', '.'), array(' [AT] ', ' [DOT] '), $string);

        case 'nonstd':
            
            $return = '';
            if (Smarty::$_MBSTRING) {
                require_once(SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php');
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    if ($unicode >= 126) {
                        $return .= '&#' . $unicode . ';';
                    } else {
                        $return .= chr($unicode);
                    }
                }

                return $return;
            }

            $_length = strlen($string);
            for ($_i = 0; $_i < $_length; $_i++) {
                $_ord = ord(substr($string, $_i, 1));
                
                if ($_ord >= 126) {
                    $return .= '&#' . $_ord . ';';
                } else {
                    $return .= substr($string, $_i, 1);
                }
            }

            return $return;

        default:
            return $string;
    }
}
