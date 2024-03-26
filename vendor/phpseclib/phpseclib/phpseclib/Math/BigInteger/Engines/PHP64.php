<?php



namespace phpseclib3\Math\BigInteger\Engines;


class PHP64 extends PHP
{
    
    const BASE = 31;
    const BASE_FULL = 0x80000000;
    const MAX_DIGIT = 0x7FFFFFFF;
    const MSB = 0x40000000;

    
    const MAX10 = 1000000000;

    
    const MAX10LEN = 9;
    const MAX_DIGIT2 = 4611686018427387904;

    
    protected function initialize($base)
    {
        if ($base != 256 && $base != -256) {
            return parent::initialize($base);
        }

        $val = $this->value;
        $this->value = [];
        $vals = &$this->value;
        $i = strlen($val);
        if (!$i) {
            return;
        }

        while (true) {
            $i -= 4;
            if ($i < 0) {
                if ($i == -4) {
                    break;
                }
                $val = substr($val, 0, 4 + $i);
                $val = str_pad($val, 4, "\0", STR_PAD_LEFT);
                if ($val == "\0\0\0\0") {
                    break;
                }
                $i = 0;
            }
            list(, $digit) = unpack('N', substr($val, $i, 4));
            $step = count($vals) & 7;
            if (!$step) {
                $digit &= static::MAX_DIGIT;
                $i++;
            } else {
                $shift = 8 - $step;
                $digit >>= $shift;
                $shift = 32 - $shift;
                $digit &= (1 << $shift) - 1;
                $temp = $i > 0 ? ord($val[$i - 1]) : 0;
                $digit |= ($temp << $shift) & 0x7F000000;
            }
            $vals[] = $digit;
        }
        while (end($vals) === 0) {
            array_pop($vals);
        }
        reset($vals);
    }

    
    public static function isValidEngine()
    {
        return PHP_INT_SIZE >= 8 && !self::testJITOnWindows();
    }

    
    public function add(PHP64 $y)
    {
        $temp = self::addHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function subtract(PHP64 $y)
    {
        $temp = self::subtractHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function multiply(PHP64 $y)
    {
        $temp = self::multiplyHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function divide(PHP64 $y)
    {
        return $this->divideHelper($y);
    }

    
    public function modInverse(PHP64 $n)
    {
        return $this->modInverseHelper($n);
    }

    
    public function extendedGCD(PHP64 $n)
    {
        return $this->extendedGCDHelper($n);
    }

    
    public function gcd(PHP64 $n)
    {
        return $this->extendedGCD($n)['gcd'];
    }

    
    public function bitwise_and(PHP64 $x)
    {
        return $this->bitwiseAndHelper($x);
    }

    
    public function bitwise_or(PHP64 $x)
    {
        return $this->bitwiseOrHelper($x);
    }

    
    public function bitwise_xor(PHP64 $x)
    {
        return $this->bitwiseXorHelper($x);
    }

    
    public function compare(PHP64 $y)
    {
        return parent::compareHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
    }

    
    public function equals(PHP64 $x)
    {
        return $this->value === $x->value && $this->is_negative == $x->is_negative;
    }

    
    public function modPow(PHP64 $e, PHP64 $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    public function powMod(PHP64 $e, PHP64 $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    public static function randomRangePrime(PHP64 $min, PHP64 $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }

    
    public static function randomRange(PHP64 $min, PHP64 $max)
    {
        return self::randomRangeHelper($min, $max);
    }

    
    public function pow(PHP64 $n)
    {
        return $this->powHelper($n);
    }

    
    public static function min(PHP64 ...$nums)
    {
        return self::minHelper($nums);
    }

    
    public static function max(PHP64 ...$nums)
    {
        return self::maxHelper($nums);
    }

    
    public function between(PHP64 $min, PHP64 $max)
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }
}
