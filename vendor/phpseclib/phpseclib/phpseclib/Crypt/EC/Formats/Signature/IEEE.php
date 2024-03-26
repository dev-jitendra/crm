<?php



namespace phpseclib3\Crypt\EC\Formats\Signature;

use phpseclib3\Math\BigInteger;


abstract class IEEE
{
    
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return false;
        }

        $len = strlen($sig);
        if ($len & 1) {
            return false;
        }

        $r = new BigInteger(substr($sig, 0, $len >> 1), 256);
        $s = new BigInteger(substr($sig, $len >> 1), 256);

        return compact('r', 's');
    }

    
    public static function save(BigInteger $r, BigInteger $s)
    {
        $r = $r->toBytes();
        $s = $s->toBytes();
        $len = max(strlen($r), strlen($s));
        return str_pad($r, $len, "\0", STR_PAD_LEFT) . str_pad($s, $len, "\0", STR_PAD_LEFT);
    }
}
