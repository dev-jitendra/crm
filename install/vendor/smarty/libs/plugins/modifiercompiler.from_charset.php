<?php



function smarty_modifiercompiler_from_charset($params, $compiler)
{
    if (!Smarty::$_MBSTRING) {
        
        return $params[0];
    }

    if (!isset($params[1])) {
        $params[1] = '"ISO-8859-1"';
    }

    return 'mb_convert_encoding(' . $params[0] . ', "' . addslashes(Smarty::$_CHARSET) . '", ' . $params[1] . ')';
}
