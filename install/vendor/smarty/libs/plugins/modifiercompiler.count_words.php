<?php



function smarty_modifiercompiler_count_words($params, $compiler)
{
    if (Smarty::$_MBSTRING) {
        
        
        return 'preg_match_all(\'/\p{L}[\p{L}\p{Mn}\p{Pd}\\\'\x{2019}]*/' . Smarty::$_UTF8_MODIFIER . '\', ' . $params[0] . ', $tmp)';
    }
    
    return 'str_word_count(' . $params[0] . ')';
}
