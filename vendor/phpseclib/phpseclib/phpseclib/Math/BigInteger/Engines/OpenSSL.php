<?php



namespace phpseclib3\Math\BigInteger\Engines;

use phpseclib3\Crypt\RSA\Formats\Keys\PKCS8;
use phpseclib3\Math\BigInteger;


abstract class OpenSSL
{
    
    public static function isValidEngine()
    {
        return extension_loaded('openssl') && static::class != __CLASS__;
    }

    
    public static function powModHelper(Engine $x, Engine $e, Engine $n)
    {
        if ($n->getLengthInBytes() < 31 || $n->getLengthInBytes() > 16384) {
            throw new \OutOfRangeException('Only modulo between 31 and 16384 bits are accepted');
        }

        $key = PKCS8::savePublicKey(
            new BigInteger($n),
            new BigInteger($e)
        );

        $plaintext = str_pad($x->toBytes(), $n->getLengthInBytes(), "\0", STR_PAD_LEFT);

        
        
        
        
        if (!openssl_public_encrypt($plaintext, $result, $key, OPENSSL_NO_PADDING)) {
            throw new \UnexpectedValueException(openssl_error_string());
        }

        $class = get_class($x);
        return new $class($result, 256);
    }
}
