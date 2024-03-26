<?php



namespace phpseclib3\Math\BigInteger\Engines\PHP;

use phpseclib3\Math\BigInteger\Engines\PHP;


abstract class Base extends PHP
{
    
    const VARIABLE = 0;
    
    const DATA = 1;

    
    public static function isValidEngine()
    {
        return static::class != __CLASS__;
    }

    
    protected static function powModHelper(PHP $x, PHP $e, PHP $n, $class)
    {
        if (empty($e->value)) {
            $temp = new $class();
            $temp->value = [1];
            return $x->normalize($temp);
        }

        if ($e->value == [1]) {
            list(, $temp) = $x->divide($n);
            return $x->normalize($temp);
        }

        if ($e->value == [2]) {
            $temp = new $class();
            $temp->value = $class::square($x->value);
            list(, $temp) = $temp->divide($n);
            return $x->normalize($temp);
        }

        return $x->normalize(static::slidingWindow($x, $e, $n, $class));
    }

    
    protected static function prepareReduce(array $x, array $n, $class)
    {
        return static::reduce($x, $n, $class);
    }

    
    protected static function multiplyReduce(array $x, array $y, array $n, $class)
    {
        $temp = $class::multiplyHelper($x, false, $y, false);
        return static::reduce($temp[self::VALUE], $n, $class);
    }

    
    protected static function squareReduce(array $x, array $n, $class)
    {
        return static::reduce($class::square($x), $n, $class);
    }
}
