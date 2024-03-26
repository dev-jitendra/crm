<?php



namespace phpseclib3\Crypt\EC\Curves;

use phpseclib3\Crypt\EC\BaseCurves\Montgomery;
use phpseclib3\Math\BigInteger;

class Curve448 extends Montgomery
{
    public function __construct()
    {
        
        $this->setModulo(new BigInteger(
            'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFE' .
            'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
            16
        ));
        $this->a24 = $this->factory->newInteger(new BigInteger('39081'));
        $this->p = [$this->factory->newInteger(new BigInteger(5))];
        
        $this->setOrder(new BigInteger(
            '3FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' .
            '7CCA23E9C44EDB49AED63690216CC2728DC58F552378C292AB5844F3',
            16
        ));

        
    }

    
    public function multiplyPoint(array $p, BigInteger $d)
    {
        
        

        $d = $d->toBytes();
        $d[0] = $d[0] & "\xFC";
        $d = strrev($d);
        $d |= "\x80";
        $d = new BigInteger($d, 256);

        return parent::multiplyPoint($p, $d);
    }

    
    public function createRandomMultiplier()
    {
        return BigInteger::random(446);
    }

    
    public function rangeCheck(BigInteger $x)
    {
        if ($x->getLength() > 448 || $x->isNegative()) {
            throw new \RangeException('x must be a positive integer less than 446 bytes in length');
        }
    }
}
