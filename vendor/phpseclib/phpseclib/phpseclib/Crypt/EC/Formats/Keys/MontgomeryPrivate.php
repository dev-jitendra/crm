<?php



namespace phpseclib3\Crypt\EC\Formats\Keys;

use phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use phpseclib3\Crypt\EC\Curves\Curve25519;
use phpseclib3\Crypt\EC\Curves\Curve448;
use phpseclib3\Exception\UnsupportedFormatException;
use phpseclib3\Math\BigInteger;


abstract class MontgomeryPrivate
{
    
    const IS_INVISIBLE = true;

    
    public static function load($key, $password = '')
    {
        switch (strlen($key)) {
            case 32:
                $curve = new Curve25519();
                break;
            case 56:
                $curve = new Curve448();
                break;
            default:
                throw new \LengthException('The only supported lengths are 32 and 56');
        }

        $components = ['curve' => $curve];
        $components['dA'] = new BigInteger($key, 256);
        $curve->rangeCheck($components['dA']);
        
        $components['QA'] = $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);

        return $components;
    }

    
    public static function savePublicKey(MontgomeryCurve $curve, array $publicKey)
    {
        return strrev($publicKey[0]->toBytes());
    }

    
    public static function savePrivateKey(BigInteger $privateKey, MontgomeryCurve $curve, array $publicKey, $secret = null, $password = '')
    {
        if (!empty($password) && is_string($password)) {
            throw new UnsupportedFormatException('MontgomeryPrivate private keys do not support encryption');
        }

        return $privateKey->toBytes();
    }
}
