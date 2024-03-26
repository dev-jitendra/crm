<?php



namespace phpseclib3\Crypt\EC\Curves;

use phpseclib3\Crypt\EC\BaseCurves\Montgomery;
use phpseclib3\Math\BigInteger;

class Curve25519 extends Montgomery
{
    public function __construct()
    {
        
        $this->setModulo(new BigInteger('7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFED', 16));
        $this->a24 = $this->factory->newInteger(new BigInteger('121666'));
        $this->p = [$this->factory->newInteger(new BigInteger(9))];
        
        $this->setOrder(new BigInteger('1000000000000000000000000000000014DEF9DEA2F79CD65812631A5CF5D3ED', 16));

        
    }

    
    public function multiplyPoint(array $p, BigInteger $d)
    {
        
        

        $d = $d->toBytes();
        $d &= "\xF8" . str_repeat("\xFF", 30) . "\x7F";
        $d = strrev($d);
        $d |= "\x40";
        $d = new BigInteger($d, -256);

        return parent::multiplyPoint($p, $d);
    }

    
    public function createRandomMultiplier()
    {
        return BigInteger::random(256);
    }

    
    public function rangeCheck(BigInteger $x)
    {
        if ($x->getLength() > 256 || $x->isNegative()) {
            throw new \RangeException('x must be a positive integer less than 256 bytes in length');
        }
    }
}
