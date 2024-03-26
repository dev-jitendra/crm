<?php



namespace phpseclib3\Crypt\DSA\Formats\Signature;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Math\BigInteger;


abstract class SSH2
{
    
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return false;
        }

        $result = Strings::unpackSSH2('ss', $sig);
        if ($result === false) {
            return false;
        }
        list($type, $blob) = $result;
        if ($type != 'ssh-dss' || strlen($blob) != 40) {
            return false;
        }

        return [
            'r' => new BigInteger(substr($blob, 0, 20), 256),
            's' => new BigInteger(substr($blob, 20), 256)
        ];
    }

    
    public static function save(BigInteger $r, BigInteger $s)
    {
        if ($r->getLength() > 160 || $s->getLength() > 160) {
            return false;
        }
        return Strings::packSSH2(
            'ss',
            'ssh-dss',
            str_pad($r->toBytes(), 20, "\0", STR_PAD_LEFT) .
            str_pad($s->toBytes(), 20, "\0", STR_PAD_LEFT)
        );
    }
}
