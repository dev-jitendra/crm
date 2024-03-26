<?php




function smarty_modifiercompiler_indent($params, $compiler)
{
    if (!isset($params[1])) {
        $params[1] = 4;
    }
    if (!isset($params[2])) {
        $params[2] = "' '";
    }

    return 'preg_replace(\'!^!m\',str_repeat(' . $params[2] . ',' . $params[1] . '),' . $params[0] . ')';
}
