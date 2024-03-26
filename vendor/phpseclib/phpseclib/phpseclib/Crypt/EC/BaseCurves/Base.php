<?php



namespace phpseclib3\Crypt\EC\BaseCurves;

use phpseclib3\Math\BigInteger;


abstract class Base
{
    
    protected $order;

    
    protected $factory;

    
    public function randomInteger()
    {
        return $this->factory->randomInteger();
    }

    
    public function convertInteger(BigInteger $x)
    {
        return $this->factory->newInteger($x);
    }

    
    public function getLengthInBytes()
    {
        return $this->factory->getLengthInBytes();
    }

    
    public function getLength()
    {
        return $this->factory->getLength();
    }

    
    public function multiplyPoint(array $p, BigInteger $d)
    {
        $alreadyInternal = isset($p[2]);
        $r = $alreadyInternal ?
            [[], $p] :
            [[], $this->convertToInternal($p)];

        $d = $d->toBits();
        for ($i = 0; $i < strlen($d); $i++) {
            $d_i = (int) $d[$i];
            $r[1 - $d_i] = $this->addPoint($r[0], $r[1]);
            $r[$d_i] = $this->doublePoint($r[$d_i]);
        }

        return $alreadyInternal ? $r[0] : $this->convertToAffine($r[0]);
    }

    
    public function createRandomMultiplier()
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }

        return BigInteger::randomRange($one, $this->order->subtract($one));
    }

    
    public function rangeCheck(BigInteger $x)
    {
        static $zero;
        if (!isset($zero)) {
            $zero = new BigInteger();
        }

        if (!isset($this->order)) {
            throw new \RuntimeException('setOrder needs to be called before this method');
        }
        if ($x->compare($this->order) > 0 || $x->compare($zero) <= 0) {
            throw new \RangeException('x must be between 1 and the order of the curve');
        }
    }

    
    public function setOrder(BigInteger $order)
    {
        $this->order = $order;
    }

    
    public function getOrder()
    {
        return $this->order;
    }

    
    public function setReduction(callable $func)
    {
        $this->factory->setReduction($func);
    }

    
    public function convertToAffine(array $p)
    {
        return $p;
    }

    
    public function convertToInternal(array $p)
    {
        return $p;
    }

    
    public function negatePoint(array $p)
    {
        $temp = [
            $p[0],
            $p[1]->negate()
        ];
        if (isset($p[2])) {
            $temp[] = $p[2];
        }
        return $temp;
    }

    
    public function multiplyAddPoints(array $points, array $scalars)
    {
        $p1 = $this->convertToInternal($points[0]);
        $p2 = $this->convertToInternal($points[1]);
        $p1 = $this->multiplyPoint($p1, $scalars[0]);
        $p2 = $this->multiplyPoint($p2, $scalars[1]);
        $r = $this->addPoint($p1, $p2);
        return $this->convertToAffine($r);
    }
}
