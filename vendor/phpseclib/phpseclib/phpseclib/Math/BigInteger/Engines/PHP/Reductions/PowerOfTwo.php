<?php



namespace phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use phpseclib3\Math\BigInteger\Engines\PHP\Base;


abstract class PowerOfTwo extends Base
{
    
    protected static function prepareReduce(array $x, array $n, $class)
    {
        return self::reduce($x, $n, $class);
    }

    
    protected static function reduce(array $x, array $n, $class)
    {
        $lhs = new $class();
        $lhs->value = $x;
        $rhs = new $class();
        $rhs->value = $n;

        $temp = new $class();
        $temp->value = [1];

        $result = $lhs->bitwise_and($rhs->subtract($temp));
        return $result->value;
    }
}
