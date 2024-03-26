<?php



namespace phpseclib3\Crypt\DSA\Formats\Signature;

use phpseclib3\File\ASN1 as Encoder;
use phpseclib3\File\ASN1\Maps;
use phpseclib3\Math\BigInteger;


abstract class ASN1
{
    
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return false;
        }

        $decoded = Encoder::decodeBER($sig);
        if (empty($decoded)) {
            return false;
        }
        $components = Encoder::asn1map($decoded[0], Maps\DssSigValue::MAP);

        return $components;
    }

    
    public static function save(BigInteger $r, BigInteger $s)
    {
        return Encoder::encodeDER(compact('r', 's'), Maps\DssSigValue::MAP);
    }
}
