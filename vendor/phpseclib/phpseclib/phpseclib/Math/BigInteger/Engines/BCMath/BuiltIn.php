<?php



namespace phpseclib3\Math\BigInteger\Engines\BCMath;

use phpseclib3\Math\BigInteger\Engines\BCMath;


abstract class BuiltIn extends BCMath
{
    
    protected static function powModHelper(BCMath $x, BCMath $e, BCMath $n)
    {
        $temp = new BCMath();
        $temp->value = bcpowmod($x->value, $e->value, $n->value);

        return $x->normalize($temp);
    }
}
