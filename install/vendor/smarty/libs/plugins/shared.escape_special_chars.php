<?php


if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
    
    function smarty_function_escape_special_chars($string)
    {
        if (!is_array($string)) {
            $string = htmlspecialchars($string, ENT_COMPAT, Smarty::$_CHARSET, false);
        }

        return $string;
    }
} else {
    
    function smarty_function_escape_special_chars($string)
    {
        if (!is_array($string)) {
            $string = preg_replace('!&(#?\w+);!', '%%%SMARTY_START%%%\\1%%%SMARTY_END%%%', $string);
            $string = htmlspecialchars($string);
            $string = str_replace(array('%%%SMARTY_START%%%', '%%%SMARTY_END%%%'), array('&', ';'), $string);
        }

        return $string;
    }
}
