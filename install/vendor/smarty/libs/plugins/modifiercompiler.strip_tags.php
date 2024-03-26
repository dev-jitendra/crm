<?php



function smarty_modifiercompiler_strip_tags($params, $compiler)
{
 if (!isset($params[1]) || $params[1] === true ||  trim($params[1],'"') == 'true') {
         return "preg_replace('!<[^>]*?>!', ' ', {$params[0]})";
    } else {
        return 'strip_tags(' . $params[0] . ')';
    }
}
