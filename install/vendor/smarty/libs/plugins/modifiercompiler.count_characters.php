<?php



function smarty_modifiercompiler_count_characters($params, $compiler)
{
    if (!isset($params[1]) || $params[1] != 'true') {
        return 'preg_match_all(\'/[^\s]/' . Smarty::$_UTF8_MODIFIER . '\',' . $params[0] . ', $tmp)';
    }
    if (Smarty::$_MBSTRING) {
        return 'mb_strlen(' . $params[0] . ', \'' . addslashes(Smarty::$_CHARSET) . '\')';
    }
    
    return 'strlen(' . $params[0] . ')';
}
