<?php



namespace phpseclib3\Crypt\Common\Formats\Signature;

use phpseclib3\Math\BigInteger;


abstract class Raw
{
    
    public static function load($sig)
    {
        switch (true) {
            case !is_array($sig):
            case !isset($sig['r']) || !isset($sig['s']):
            case !$sig['r'] instanceof BigInteger:
            case !$sig['s'] instanceof BigInteger:
                return false;
        }

        return [
            'r' => $sig['r'],
            's' => $sig['s']
        ];
    }

    
    public static function save(BigInteger $r, BigInteger $s)
    {
        return compact('r', 's');
    }
}
