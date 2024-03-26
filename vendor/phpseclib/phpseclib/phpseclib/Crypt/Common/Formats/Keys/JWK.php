<?php



namespace phpseclib3\Crypt\Common\Formats\Keys;

use phpseclib3\Common\Functions\Strings;


abstract class JWK
{
    
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new \UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }

        $key = preg_replace('#\s#', '', $key); 

        if (PHP_VERSION_ID >= 73000) {
            $key = json_decode($key, null, 512, JSON_THROW_ON_ERROR);
        } else {
            $key = json_decode($key);
            if (!$key) {
                throw new \RuntimeException('Unable to decode JSON');
            }
        }

        if (isset($key->kty)) {
            return $key;
        }

        if (count($key->keys) != 1) {
            throw new \RuntimeException('Although the JWK key format supports multiple keys phpseclib does not');
        }

        return $key->keys[0];
    }

    
    protected static function wrapKey(array $key, array $options)
    {
        return json_encode(['keys' => [$key + $options]]);
    }
}
