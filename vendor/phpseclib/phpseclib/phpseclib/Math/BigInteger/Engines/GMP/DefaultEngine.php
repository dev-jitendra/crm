<?php



namespace phpseclib3\Math\BigInteger\Engines\GMP;

use phpseclib3\Math\BigInteger\Engines\GMP;


abstract class DefaultEngine extends GMP
{
    
    protected static function powModHelper(GMP $x, GMP $e, GMP $n)
    {
        $temp = new GMP();
        $temp->value = gmp_powm($x->value, $e->value, $n->value);

        return $x->normalize($temp);
    }
}
