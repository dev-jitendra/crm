<?php



function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
    if ($length == 0)
        return '';

    if (Smarty::$_MBSTRING) {
        if (mb_strlen($string, Smarty::$_CHARSET) > $length) {
            $length -= min($length, mb_strlen($etc, Smarty::$_CHARSET));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/' . Smarty::$_UTF8_MODIFIER, '', mb_substr($string, 0, $length + 1, Smarty::$_CHARSET));
            }
            if (!$middle) {
                return mb_substr($string, 0, $length, Smarty::$_CHARSET) . $etc;
            }

            return mb_substr($string, 0, $length / 2, Smarty::$_CHARSET) . $etc . mb_substr($string, - $length / 2, $length, Smarty::$_CHARSET);
        }

        return $string;
    }

    
    if (isset($string[$length])) {
        $length -= min($length, strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
        }
        if (!$middle) {
            return substr($string, 0, $length) . $etc;
        }

        return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
    }

    return $string;
}
