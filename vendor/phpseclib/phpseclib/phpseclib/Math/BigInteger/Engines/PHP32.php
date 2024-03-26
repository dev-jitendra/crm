<?php



namespace phpseclib3\Math\BigInteger\Engines;


class PHP32 extends PHP
{
    
    const BASE = 26;
    const BASE_FULL = 0x4000000;
    const MAX_DIGIT = 0x3FFFFFF;
    const MSB = 0x2000000;

    
    const MAX10 = 10000000;

    
    const MAX10LEN = 7;
    const MAX_DIGIT2 = 4503599627370496;

    
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
            if ($digit < 0) {
                $digit += 0xFFFFFFFF + 1;
            }
            $step = count($vals) & 3;
            if ($step) {
                $digit = (int) floor($digit / pow(2, 2 * $step));
            }
            if ($step != 3) {
                $digit = (int) fmod($digit, static::BASE_FULL);
                $i++;
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
        return PHP_INT_SIZE >= 4 && !self::testJITOnWindows();
    }

    
    public function add(PHP32 $y)
    {
        $temp = self::addHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function subtract(PHP32 $y)
    {
        $temp = self::subtractHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function multiply(PHP32 $y)
    {
        $temp = self::multiplyHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    
    public function divide(PHP32 $y)
    {
        return $this->divideHelper($y);
    }

    
    public function modInverse(PHP32 $n)
    {
        return $this->modInverseHelper($n);
    }

    
    public function extendedGCD(PHP32 $n)
    {
        return $this->extendedGCDHelper($n);
    }

    
    public function gcd(PHP32 $n)
    {
        return $this->extendedGCD($n)['gcd'];
    }

    
    public function bitwise_and(PHP32 $x)
    {
        return $this->bitwiseAndHelper($x);
    }

    
    public function bitwise_or(PHP32 $x)
    {
        return $this->bitwiseOrHelper($x);
    }

    
    public function bitwise_xor(PHP32 $x)
    {
        return $this->bitwiseXorHelper($x);
    }

    
    public function compare(PHP32 $y)
    {
        return $this->compareHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
    }

    
    public function equals(PHP32 $x)
    {
        return $this->value === $x->value && $this->is_negative == $x->is_negative;
    }

    
    public function modPow(PHP32 $e, PHP32 $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    public function powMod(PHP32 $e, PHP32 $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    public static function randomRangePrime(PHP32 $min, PHP32 $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }

    
    public static function randomRange(PHP32 $min, PHP32 $max)
    {
        return self::randomRangeHelper($min, $max);
    }

    
    public function pow(PHP32 $n)
    {
        return $this->powHelper($n);
    }

    
    public static function min(PHP32 ...$nums)
    {
        return self::minHelper($nums);
    }

    
    public static function max(PHP32 ...$nums)
    {
        return self::maxHelper($nums);
    }

    
    public function between(PHP32 $min, PHP32 $max)
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }
}
