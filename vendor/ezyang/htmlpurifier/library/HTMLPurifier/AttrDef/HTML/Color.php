<?php


class HTMLPurifier_AttrDef_HTML_Color extends HTMLPurifier_AttrDef
{

    
    public function validate($string, $config, $context)
    {
        static $colors = null;
        if ($colors === null) {
            $colors = $config->get('Core.ColorKeywords');
        }

        $string = trim($string);

        if (empty($string)) {
            return false;
        }
        $lower = strtolower($string);
        if (isset($colors[$lower])) {
            return $colors[$lower];
        }
        if ($string[0] === '#') {
            $hex = substr($string, 1);
        } else {
            $hex = $string;
        }

        $length = strlen($hex);
        if ($length !== 3 && $length !== 6) {
            return false;
        }
        if (!ctype_xdigit($hex)) {
            return false;
        }
        if ($length === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return "#$hex";
    }
}


