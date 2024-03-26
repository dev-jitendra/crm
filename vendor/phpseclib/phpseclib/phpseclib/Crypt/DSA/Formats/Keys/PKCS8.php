<?php



namespace phpseclib3\Crypt\DSA\Formats\Keys;

use phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use phpseclib3\File\ASN1;
use phpseclib3\File\ASN1\Maps;
use phpseclib3\Math\BigInteger;


abstract class PKCS8 extends Progenitor
{
    
    const OID_NAME = 'id-dsa';

    
    const OID_VALUE = '1.2.840.10040.4.1';

    
    protected static $childOIDsLoaded = false;

    
    public static function load($key, $password = '')
    {
        $key = parent::load($key, $password);

        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';

        $decoded = ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (!$decoded) {
            throw new \RuntimeException('Unable to decode BER of parameters');
        }
        $components = ASN1::asn1map($decoded[0], Maps\DSAParams::MAP);
        if (!is_array($components)) {
            throw new \RuntimeException('Unable to perform ASN1 mapping on parameters');
        }

        $decoded = ASN1::decodeBER($key[$type]);
        if (empty($decoded)) {
            throw new \RuntimeException('Unable to decode BER');
        }

        $var = $type == 'privateKey' ? 'x' : 'y';
        $components[$var] = ASN1::asn1map($decoded[0], Maps\DSAPublicKey::MAP);
        if (!$components[$var] instanceof BigInteger) {
            throw new \RuntimeException('Unable to perform ASN1 mapping');
        }

        if (isset($key['meta'])) {
            $components['meta'] = $key['meta'];
        }

        return $components;
    }

    
    public static function savePrivateKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, BigInteger $x, $password = '', array $options = [])
    {
        $params = [
            'p' => $p,
            'q' => $q,
            'g' => $g
        ];
        $params = ASN1::encodeDER($params, Maps\DSAParams::MAP);
        $params = new ASN1\Element($params);
        $key = ASN1::encodeDER($x, Maps\DSAPublicKey::MAP);
        return self::wrapPrivateKey($key, [], $params, $password, null, '', $options);
    }

    
    public static function savePublicKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, array $options = [])
    {
        $params = [
            'p' => $p,
            'q' => $q,
            'g' => $g
        ];
        $params = ASN1::encodeDER($params, Maps\DSAParams::MAP);
        $params = new ASN1\Element($params);
        $key = ASN1::encodeDER($y, Maps\DSAPublicKey::MAP);
        return self::wrapPublicKey($key, $params);
    }
}
