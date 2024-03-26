<?php




function smarty_modifiercompiler_lower($params, $compiler)
{
    if (Smarty::$_MBSTRING) {
        return 'mb_strtolower(' . $params[0] . ', \'' . addslashes(Smarty::$_CHARSET) . '\')' ;
    }
    
    return 'strtolower(' . $params[0] . ')';
}
