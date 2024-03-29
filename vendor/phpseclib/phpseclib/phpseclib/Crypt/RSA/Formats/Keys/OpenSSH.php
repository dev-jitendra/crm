<?php



namespace phpseclib3\Crypt\RSA\Formats\Keys;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Crypt\Common\Formats\Keys\OpenSSH as Progenitor;
use phpseclib3\Math\BigInteger;


abstract class OpenSSH extends Progenitor
{
    
    protected static $types = ['ssh-rsa'];

    
    public static function load($key, $password = '')
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }

        $parsed = parent::load($key, $password);

        if (isset($parsed['paddedKey'])) {
            list($type) = Strings::unpackSSH2('s', $parsed['paddedKey']);
            if ($type != $parsed['type']) {
                throw new \RuntimeException("The public and private keys are not of the same type ($type vs $parsed[type])");
            }

            $primes = $coefficients = [];

            list(
                $modulus,
                $publicExponent,
                $privateExponent,
                $coefficients[2],
                $primes[1],
                $primes[2],
                $comment,
            ) = Strings::unpackSSH2('i6s', $parsed['paddedKey']);

            $temp = $primes[1]->subtract($one);
            $exponents = [1 => $publicExponent->modInverse($temp)];
            $temp = $primes[2]->subtract($one);
            $exponents[] = $publicExponent->modInverse($temp);

            $isPublicKey = false;

            return compact('publicExponent', 'modulus', 'privateExponent', 'primes', 'coefficients', 'exponents', 'comment', 'isPublicKey');
        }

        list($publicExponent, $modulus) = Strings::unpackSSH2('ii', $parsed['publicKey']);

        return [
            'isPublicKey' => true,
            'modulus' => $modulus,
            'publicExponent' => $publicExponent,
            'comment' => $parsed['comment']
        ];
    }

    
    public static function savePublicKey(BigInteger $n, BigInteger $e, array $options = [])
    {
        $RSAPublicKey = Strings::packSSH2('sii', 'ssh-rsa', $e, $n);

        if (isset($options['binary']) ? $options['binary'] : self::$binary) {
            return $RSAPublicKey;
        }

        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $RSAPublicKey = 'ssh-rsa ' . base64_encode($RSAPublicKey) . ' ' . $comment;

        return $RSAPublicKey;
    }

    
    public static function savePrivateKey(BigInteger $n, BigInteger $e, BigInteger $d, array $primes, array $exponents, array $coefficients, $password = '', array $options = [])
    {
        $publicKey = self::savePublicKey($n, $e, ['binary' => true]);
        $privateKey = Strings::packSSH2('si6', 'ssh-rsa', $n, $e, $d, $coefficients[2], $primes[1], $primes[2]);

        return self::wrapPrivateKey($publicKey, $privateKey, $password, $options);
    }
}
