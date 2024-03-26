<?php



namespace phpseclib3\Crypt\DSA\Formats\Keys;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Crypt\Common\Formats\Keys\PKCS1 as Progenitor;
use phpseclib3\File\ASN1;
use phpseclib3\File\ASN1\Maps;
use phpseclib3\Math\BigInteger;


abstract class PKCS1 extends Progenitor
{
    
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);

        $decoded = ASN1::decodeBER($key);
        if (!$decoded) {
            throw new \RuntimeException('Unable to decode BER');
        }

        $key = ASN1::asn1map($decoded[0], Maps\DSAParams::MAP);
        if (is_array($key)) {
            return $key;
        }

        $key = ASN1::asn1map($decoded[0], Maps\DSAPrivateKey::MAP);
        if (is_array($key)) {
            return $key;
        }

        $key = ASN1::asn1map($decoded[0], Maps\DSAPublicKey::MAP);
        if (is_array($key)) {
            return $key;
        }

        throw new \RuntimeException('Unable to perform ASN1 mapping');
    }

    
    public static function saveParameters(BigInteger $p, BigInteger $q, BigInteger $g)
    {
        $key = [
            'p' => $p,
            'q' => $q,
            'g' => $g
        ];

        $key = ASN1::encodeDER($key, Maps\DSAParams::MAP);

        return "-----BEGIN DSA PARAMETERS-----\r\n" .
               chunk_split(Strings::base64_encode($key), 64) .
               "-----END DSA PARAMETERS-----\r\n";
    }

    
    public static function savePrivateKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, BigInteger $x, $password = '', array $options = [])
    {
        $key = [
            'version' => 0,
            'p' => $p,
            'q' => $q,
            'g' => $g,
            'y' => $y,
            'x' => $x
        ];

        $key = ASN1::encodeDER($key, Maps\DSAPrivateKey::MAP);

        return self::wrapPrivateKey($key, 'DSA', $password, $options);
    }

    
    public static function savePublicKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y)
    {
        $key = ASN1::encodeDER($y, Maps\DSAPublicKey::MAP);

        return self::wrapPublicKey($key, 'DSA');
    }
}
