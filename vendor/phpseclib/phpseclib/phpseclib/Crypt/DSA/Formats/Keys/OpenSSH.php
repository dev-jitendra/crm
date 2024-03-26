<?php



namespace phpseclib3\Crypt\DSA\Formats\Keys;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
use phpseclib3\Math\BigInteger;


abstract class OpenSSH extends Progenitor
{
    
    protected static $types = ['ssh-dss'];

    
    public static function load($key, $password = '')
    {
        $parsed = parent::load($key, $password);

        if (isset($parsed['paddedKey'])) {
            list($type) = Strings::unpackSSH2('s', $parsed['paddedKey']);
            if ($type != $parsed['type']) {
                throw new \RuntimeException("The public and private keys are not of the same type ($type vs $parsed[type])");
            }

            list($p, $q, $g, $y, $x, $comment) = Strings::unpackSSH2('i5s', $parsed['paddedKey']);

            return compact('p', 'q', 'g', 'y', 'x', 'comment');
        }

        list($p, $q, $g, $y) = Strings::unpackSSH2('iiii', $parsed['publicKey']);

        $comment = $parsed['comment'];

        return compact('p', 'q', 'g', 'y', 'comment');
    }

    
    public static function savePublicKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, array $options = [])
    {
        if ($q->getLength() != 160) {
            throw new \InvalidArgumentException('SSH only supports keys with an N (length of Group Order q) of 160');
        }

        
        
        
        
        
        
        $DSAPublicKey = Strings::packSSH2('siiii', 'ssh-dss', $p, $q, $g, $y);

        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $DSAPublicKey;
        }

        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $DSAPublicKey = 'ssh-dss ' . base64_encode($DSAPublicKey) . ' ' . $comment;

        return $DSAPublicKey;
    }

    
    public static function savePrivateKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, BigInteger $x, $password = '', array $options = [])
    {
        $publicKey = self::savePublicKey($p, $q, $g, $y, ['binary' => true]);
        $privateKey = Strings::packSSH2('si5', 'ssh-dss', $p, $q, $g, $y, $x);

        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
