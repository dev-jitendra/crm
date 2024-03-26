<?php




namespace LightnCandy;


class Expression
{
    
    public static function boolString($v)
    {
        return ($v > 0) ? 'true' : 'false';
    }

    
    public static function listString($list)
    {
        return implode(',', (array_map(function ($v) {
            return "'$v'";
        }, $list)));
    }

    
    public static function arrayString($list)
    {
        return implode('', (array_map(function ($v) {
            return "['$v']";
        }, $list)));
    }

    
    public static function analyze($context, $var)
    {
        $levels = 0;
        $spvar = false;

        if (isset($var[0])) {
            
            if (!is_string($var[0]) && is_int($var[0])) {
                $levels = array_shift($var);
            }
        }

        if (isset($var[0])) {
            
            if ($context['flags']['spvar']) {
                if (substr($var[0], 0, 1) === '@') {
                    $spvar = true;
                    $var[0] = substr($var[0], 1);
                }
            }
        }

        return array($levels, $spvar, $var);
    }

    
    public static function toString($levels, $spvar, $var)
    {
        return ($spvar ? '@' : '') . str_repeat('../', $levels) . ((is_array($var) && count($var)) ? implode('.', array_map(function ($v) {
            return ($v === null) ? 'this' : "[$v]";
        }, $var)) : 'this');
    }
}
