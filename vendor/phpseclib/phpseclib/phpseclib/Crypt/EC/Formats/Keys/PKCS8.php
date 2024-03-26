<?php



namespace phpseclib3\Crypt\EC\Formats\Keys;

use phpseclib3\Crypt\Common\Formats\Keys\PKCS8 as Progenitor;
use phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use phpseclib3\Crypt\EC\BaseCurves\Montgomery as MontgomeryCurve;
use phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use phpseclib3\Crypt\EC\Curves\Ed25519;
use phpseclib3\Crypt\EC\Curves\Ed448;
use phpseclib3\Exception\UnsupportedCurveException;
use phpseclib3\File\ASN1;
use phpseclib3\File\ASN1\Maps;
use phpseclib3\Math\BigInteger;


abstract class PKCS8 extends Progenitor
{
    use Common;

    
    const OID_NAME = ['id-ecPublicKey', 'id-Ed25519', 'id-Ed448'];

    
    const OID_VALUE = ['1.2.840.10045.2.1', '1.3.101.112', '1.3.101.113'];

    
    public static function load($key, $password = '')
    {
        
        
        
        
        
        self::initialize_static_variables();

        $key = parent::load($key, $password);

        $type = isset($key['privateKey']) ? 'privateKey' : 'publicKey';

        switch ($key[$type . 'Algorithm']['algorithm']) {
            case 'id-Ed25519':
            case 'id-Ed448':
                return self::loadEdDSA($key);
        }

        $decoded = ASN1::decodeBER($key[$type . 'Algorithm']['parameters']->element);
        if (!$decoded) {
            throw new \RuntimeException('Unable to decode BER');
        }
        $params = ASN1::asn1map($decoded[0], Maps\ECParameters::MAP);
        if (!$params) {
            throw new \RuntimeException('Unable to decode the parameters using Maps\ECParameters');
        }

        $components = [];
        $components['curve'] = self::loadCurveByParam($params);

        if ($type == 'publicKey') {
            $components['QA'] = self::extractPoint("\0" . $key['publicKey'], $components['curve']);

            return $components;
        }

        $decoded = ASN1::decodeBER($key['privateKey']);
        if (!$decoded) {
            throw new \RuntimeException('Unable to decode BER');
        }
        $key = ASN1::asn1map($decoded[0], Maps\ECPrivateKey::MAP);
        if (isset($key['parameters']) && $params != $key['parameters']) {
            throw new \RuntimeException('The PKCS8 parameter field does not match the private key parameter field');
        }

        $components['dA'] = new BigInteger($key['privateKey'], 256);
        $components['curve']->rangeCheck($components['dA']);
        $components['QA'] = isset($key['publicKey']) ?
            self::extractPoint($key['publicKey'], $components['curve']) :
            $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);

        return $components;
    }

    
    private static function loadEdDSA(array $key)
    {
        $components = [];

        if (isset($key['privateKey'])) {
            $components['curve'] = $key['privateKeyAlgorithm']['algorithm'] == 'id-Ed25519' ? new Ed25519() : new Ed448();

            
            
            if (substr($key['privateKey'], 0, 2) != "\x04\x20") {
                throw new \RuntimeException('The first two bytes of the private key field should be 0x0420');
            }
            $arr = $components['curve']->extractSecret(substr($key['privateKey'], 2));
            $components['dA'] = $arr['dA'];
            $components['secret'] = $arr['secret'];
        }

        if (isset($key['publicKey'])) {
            if (!isset($components['curve'])) {
                $components['curve'] = $key['publicKeyAlgorithm']['algorithm'] == 'id-Ed25519' ? new Ed25519() : new Ed448();
            }

            $components['QA'] = self::extractPoint($key['publicKey'], $components['curve']);
        }

        if (isset($key['privateKey']) && !isset($components['QA'])) {
            $components['QA'] = $components['curve']->multiplyPoint($components['curve']->getBasePoint(), $components['dA']);
        }

        return $components;
    }

    
    public static function savePublicKey(BaseCurve $curve, array $publicKey, array $options = [])
    {
        self::initialize_static_variables();

        if ($curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('Montgomery Curves are not supported');
        }

        if ($curve instanceof TwistedEdwardsCurve) {
            return self::wrapPublicKey(
                $curve->encodePoint($publicKey),
                null,
                $curve instanceof Ed25519 ? 'id-Ed25519' : 'id-Ed448'
            );
        }

        $params = new ASN1\Element(self::encodeParameters($curve, false, $options));

        $key = "\4" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();

        return self::wrapPublicKey($key, $params, 'id-ecPublicKey');
    }

    
    public static function savePrivateKey(BigInteger $privateKey, BaseCurve $curve, array $publicKey, $secret = null, $password = '', array $options = [])
    {
        self::initialize_static_variables();

        if ($curve instanceof MontgomeryCurve) {
            throw new UnsupportedCurveException('Montgomery Curves are not supported');
        }

        if ($curve instanceof TwistedEdwardsCurve) {
            return self::wrapPrivateKey(
                "\x04\x20" . $secret,
                [],
                null,
                $password,
                $curve instanceof Ed25519 ? 'id-Ed25519' : 'id-Ed448'
            );
        }

        $publicKey = "\4" . $publicKey[0]->toBytes() . $publicKey[1]->toBytes();

        $params = new ASN1\Element(self::encodeParameters($curve, false, $options));

        $key = [
            'version' => 'ecPrivkeyVer1',
            'privateKey' => $privateKey->toBytes(),
            
            'publicKey' => "\0" . $publicKey
        ];

        $key = ASN1::encodeDER($key, Maps\ECPrivateKey::MAP);

        return self::wrapPrivateKey($key, [], $params, $password, 'id-ecPublicKey', '', $options);
    }
}
