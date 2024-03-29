<?php


if (!function_exists('smarty_mb_wordwrap')) {

    
    function smarty_mb_wordwrap($str, $width=75, $break="\n", $cut=false)
    {
        
        $tokens = preg_split('!(\s)!S' . Smarty::$_UTF8_MODIFIER, $str, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
        $length = 0;
        $t = '';
        $_previous = false;

        foreach ($tokens as $_token) {
            $token_length = mb_strlen($_token, Smarty::$_CHARSET);
            $_tokens = array($_token);
            if ($token_length > $width) {
                
                $t = mb_substr($t, 0, -1, Smarty::$_CHARSET);
                $_previous = false;
                $length = 0;

                if ($cut) {
                    $_tokens = preg_split('!(.{' . $width . '})!S' . Smarty::$_UTF8_MODIFIER, $_token, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
                    
                    $t .= $break;
                }
            }

            foreach ($_tokens as $token) {
                $_space = !!preg_match('!^\s$!S' . Smarty::$_UTF8_MODIFIER, $token);
                $token_length = mb_strlen($token, Smarty::$_CHARSET);
                $length += $token_length;

                if ($length > $width) {
                    
                    if ($_previous && $token_length < $width) {
                        $t = mb_substr($t, 0, -1, Smarty::$_CHARSET);
                    }

                    
                    $t .= $break;
                    $length = $token_length;

                    
                    if ($_space) {
                        $length = 0;
                        continue;
                    }
                } elseif ($token == "\n") {
                    
                    $_previous = 0;
                    $length = 0;
                } else {
                    
                    $_previous = $_space;
                }
                
                $t .= $token;
            }
        }

        return $t;
    }

}
