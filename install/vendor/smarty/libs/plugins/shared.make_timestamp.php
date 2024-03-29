<?php



function smarty_make_timestamp($string)
{
    if (empty($string)) {
        
        return time();
    } elseif ($string instanceof DateTime) {
        return $string->getTimestamp();
    } elseif (strlen($string) == 14 && ctype_digit($string)) {
        
        return mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
                       substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
    } elseif (is_numeric($string)) {
        
        return (int) $string;
    } else {
        
        $time = strtotime($string);
        if ($time == -1 || $time === false) {
            
            return time();
        }

        return $time;
    }
}
