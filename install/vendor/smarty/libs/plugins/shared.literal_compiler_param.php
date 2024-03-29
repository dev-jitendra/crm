<?php



function smarty_literal_compiler_param($params, $index, $default=null)
{
    
    if (!isset($params[$index])) {
        return $default;
    }
    
    if (!preg_match('/^([\'"]?)[a-zA-Z0-9]+(\\1)$/', $params[$index])) {
        throw new SmartyException('$param[' . $index . '] is not a literal and is thus not evaluatable at compile time');
    }

    $t = null;
    eval("\$t = " . $params[$index] . ";");

    return $t;
}
