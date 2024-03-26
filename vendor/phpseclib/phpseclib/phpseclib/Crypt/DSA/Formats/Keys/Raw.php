<?php



namespace phpseclib3\Crypt\DSA\Formats\Keys;

use phpseclib3\Math\BigInteger;


abstract class Raw
{
    
    public static function load($key, $password = '')
    {
        if (!is_array($key)) {
            throw new \UnexpectedValueException('Key should be a array - not a ' . gettype($key));
        }

        switch (true) {
            case !isset($key['p']) || !isset($key['q']) || !isset($key['g']):
            case !$key['p'] instanceof BigInteger:
            case !$key['q'] instanceof BigInteger:
            case !$key['g'] instanceof BigInteger:
            case !isset($key['x']) && !isset($key['y']):
            case isset($key['x']) && !$key['x'] instanceof BigInteger:
            case isset($key['y']) && !$key['y'] instanceof BigInteger:
                throw new \UnexpectedValueException('Key appears to be malformed');
        }

        $options = ['p' => 1, 'q' => 1, 'g' => 1, 'x' => 1, 'y' => 1];

        return array_intersect_key($key, $options);
    }

    
    public static function savePrivateKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y, BigInteger $x, $password = '')
    {
        return compact('p', 'q', 'g', 'y', 'x');
    }

    
    public static function savePublicKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y)
    {
        return compact('p', 'q', 'g', 'y');
    }
}
