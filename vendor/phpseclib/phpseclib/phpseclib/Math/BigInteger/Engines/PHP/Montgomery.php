<?php



namespace phpseclib3\Math\BigInteger\Engines\PHP;

use phpseclib3\Math\BigInteger\Engines\Engine;
use phpseclib3\Math\BigInteger\Engines\PHP;
use phpseclib3\Math\BigInteger\Engines\PHP\Reductions\PowerOfTwo;


abstract class Montgomery extends Base
{
    
    public static function isValidEngine()
    {
        return static::class != __CLASS__;
    }

    
    protected static function slidingWindow(Engine $x, Engine $e, Engine $n, $class)
    {
        
        if ($n->value[0] & 1) {
            return parent::slidingWindow($x, $e, $n, $class);
        }
        

        
        for ($i = 0; $i < count($n->value); ++$i) {
            if ($n->value[$i]) {
                $temp = decbin($n->value[$i]);
                $j = strlen($temp) - strrpos($temp, '1') - 1;
                $j += $class::BASE * $i;
                break;
            }
        }
        

        $mod1 = clone $n;
        $mod1->rshift($j);
        $mod2 = new $class();
        $mod2->value = [1];
        $mod2->lshift($j);

        $part1 = $mod1->value != [1] ? parent::slidingWindow($x, $e, $mod1, $class) : new $class();
        $part2 = PowerOfTwo::slidingWindow($x, $e, $mod2, $class);

        $y1 = $mod2->modInverse($mod1);
        $y2 = $mod1->modInverse($mod2);

        $result = $part1->multiply($mod2);
        $result = $result->multiply($y1);

        $temp = $part2->multiply($mod1);
        $temp = $temp->multiply($y2);

        $result = $result->add($temp);
        list(, $result) = $result->divide($n);

        return $result;
    }
}
