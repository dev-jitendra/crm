<?php



namespace phpseclib3\Crypt\EC\BaseCurves;

use phpseclib3\Crypt\EC\Curves\Curve25519;
use phpseclib3\Math\BigInteger;
use phpseclib3\Math\PrimeField;
use phpseclib3\Math\PrimeField\Integer as PrimeInteger;


class Montgomery extends Base
{
    
    protected $factory;

    
    protected $a;

    
    protected $a24;

    
    protected $zero;

    
    protected $one;

    
    protected $p;

    
    protected $modulo;

    
    protected $order;

    
    public function setModulo(BigInteger $modulo)
    {
        $this->modulo = $modulo;
        $this->factory = new PrimeField($modulo);
        $this->zero = $this->factory->newInteger(new BigInteger());
        $this->one = $this->factory->newInteger(new BigInteger(1));
    }

    
    public function setCoefficients(BigInteger $a)
    {
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        $this->a = $this->factory->newInteger($a);
        $two = $this->factory->newInteger(new BigInteger(2));
        $four = $this->factory->newInteger(new BigInteger(4));
        $this->a24 = $this->a->subtract($two)->divide($four);
    }

    
    public function setBasePoint($x, $y)
    {
        switch (true) {
            case !$x instanceof BigInteger && !$x instanceof PrimeInteger:
                throw new \UnexpectedValueException('Argument 1 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\Integer');
            case !$y instanceof BigInteger && !$y instanceof PrimeInteger:
                throw new \UnexpectedValueException('Argument 2 passed to Prime::setBasePoint() must be an instance of either BigInteger or PrimeField\Integer');
        }
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        $this->p = [
            $x instanceof BigInteger ? $this->factory->newInteger($x) : $x,
            $y instanceof BigInteger ? $this->factory->newInteger($y) : $y
        ];
    }

    
    public function getBasePoint()
    {
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }
        
        return $this->p;
    }

    
    private function doubleAndAddPoint(array $p, array $q, PrimeInteger $x1)
    {
        if (!isset($this->factory)) {
            throw new \RuntimeException('setModulo needs to be called before this method');
        }

        if (!count($p) || !count($q)) {
            return [];
        }

        if (!isset($p[1])) {
            throw new \RuntimeException('Affine coordinates need to be manually converted to XZ coordinates');
        }

        list($x2, $z2) = $p;
        list($x3, $z3) = $q;

        $a = $x2->add($z2);
        $aa = $a->multiply($a);
        $b = $x2->subtract($z2);
        $bb = $b->multiply($b);
        $e = $aa->subtract($bb);
        $c = $x3->add($z3);
        $d = $x3->subtract($z3);
        $da = $d->multiply($a);
        $cb = $c->multiply($b);
        $temp = $da->add($cb);
        $x5 = $temp->multiply($temp);
        $temp = $da->subtract($cb);
        $z5 = $x1->multiply($temp->multiply($temp));
        $x4 = $aa->multiply($bb);
        $temp = static::class == Curve25519::class ? $bb : $aa;
        $z4 = $e->multiply($temp->add($this->a24->multiply($e)));

        return [
            [$x4, $z4],
            [$x5, $z5]
        ];
    }

    
    public function multiplyPoint(array $p, BigInteger $d)
    {
        $p1 = [$this->one, $this->zero];
        $alreadyInternal = isset($x[1]);
        $p2 = $this->convertToInternal($p);
        $x = $p[0];

        $b = $d->toBits();
        $b = str_pad($b, 256, '0', STR_PAD_LEFT);
        for ($i = 0; $i < strlen($b); $i++) {
            $b_i = (int) $b[$i];
            if ($b_i) {
                list($p2, $p1) = $this->doubleAndAddPoint($p2, $p1, $x);
            } else {
                list($p1, $p2) = $this->doubleAndAddPoint($p1, $p2, $x);
            }
        }

        return $alreadyInternal ? $p1 : $this->convertToAffine($p1);
    }

    
    public function convertToInternal(array $p)
    {
        if (empty($p)) {
            return [clone $this->zero, clone $this->one];
        }

        if (isset($p[1])) {
            return $p;
        }

        $p[1] = clone $this->one;

        return $p;
    }

    
    public function convertToAffine(array $p)
    {
        if (!isset($p[1])) {
            return $p;
        }
        list($x, $z) = $p;
        return [$x->divide($z)];
    }
}
