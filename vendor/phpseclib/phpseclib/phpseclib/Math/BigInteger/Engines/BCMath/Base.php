<?php



namespace phpseclib3\Math\BigInteger\Engines\BCMath;

use phpseclib3\Math\BigInteger\Engines\BCMath;


abstract class Base extends BCMath
{
    
    const VARIABLE = 0;
    
    const DATA = 1;

    
    public static function isValidEngine()
    {
        return static::class != __CLASS__;
    }

    
    protected static function powModHelper(BCMath $x, BCMath $e, BCMath $n, $class)
    {
        if (empty($e->value)) {
            $temp = new $class();
            $temp->value = '1';
            return $x->normalize($temp);
        }

        return $x->normalize(static::slidingWindow($x, $e, $n, $class));
    }

    
    protected static function prepareReduce($x, $n, $class)
    {
        return static::reduce($x, $n);
    }

    
    protected static function multiplyReduce($x, $y, $n, $class)
    {
        return static::reduce(bcmul($x, $y), $n);
    }

    
    protected static function squareReduce($x, $n, $class)
    {
        return static::reduce(bcmul($x, $x), $n);
    }
}
