<?php



function smarty_mb_to_unicode($string, $encoding=null)
{
    if ($encoding) {
        $expanded = mb_convert_encoding($string, "UTF-32BE", $encoding);
    } else {
        $expanded = mb_convert_encoding($string, "UTF-32BE");
    }

    return unpack("N*", $expanded);
}


function smarty_mb_from_unicode($unicode, $encoding=null)
{
    $t = '';
    if (!$encoding) {
        $encoding = mb_internal_encoding();
    }
    foreach ((array) $unicode as $utf32be) {
        $character = pack("N*", $utf32be);
        $t .= mb_convert_encoding($character, $encoding, "UTF-32BE");
    }

    return $t;
}
