<?php



function smarty_modifiercompiler_upper($params, $compiler)
{
    if (Smarty::$_MBSTRING) {
        return 'mb_strtoupper(' . $params[0] . ', \'' . addslashes(Smarty::$_CHARSET) . '\')' ;
    }
    
    return 'strtoupper(' . $params[0] . ')';
}
