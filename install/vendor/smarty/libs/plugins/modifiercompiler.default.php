<?php



function smarty_modifiercompiler_default ($params, $compiler)
{
    $output = $params[0];
    if (!isset($params[1])) {
        $params[1] = "''";
    }

    array_shift($params);
    foreach ($params as $param) {
        $output = '(($tmp = @' . $output . ')===null||$tmp===\'\' ? ' . $param . ' : $tmp)';
    }

    return $output;
}
