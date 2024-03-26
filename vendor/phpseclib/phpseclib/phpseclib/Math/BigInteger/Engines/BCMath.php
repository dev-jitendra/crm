<?php



namespace phpseclib3\Math\BigInteger\Engines;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Exception\BadConfigurationException;


class BCMath extends Engine
{
    
    const FAST_BITWISE = false;

    
    const ENGINE_DIR = 'BCMath';

    
    public static function isValidEngine()
    {
        return extension_loaded('bcmath');
    }

    
    public function __construct($x = 0, $base = 10)
    {
        if (!isset(static::$isValidEngine[static::class])) {
            static::$isValidEngine[static::class] = self::isValidEngine();
        }
        if (!static::$isValidEngine[static::class]) {
            throw new BadConfigurationException('BCMath is not setup correctly on this system');
        }

        $this->value = '0';

        parent::__construct($x, $base);
    }

    
    protected function initialize($base)
    {
        switch (abs($base)) {
            case 256:
                
                $len = (strlen($this->value) + 3) & ~3;

                $x = str_pad($this->value, $len, chr(0), STR_PAD_LEFT);

                $this->value = '0';
                for ($i = 0; $i < $len; $i += 4) {
                    $this->value = bcmul($this->value, '4294967296', 0); 
                    $this->value = bcadd(
                        $this->value,
                        0x1000000 * ord($x[$i]) + ((ord($x[$i + 1]) << 16) | (ord(
                            $x[$i + 2]
                        ) << 8) | ord($x[$i + 3])),
                        0
                    );
                }

                if ($this->is_negative) {
                    $this->value = '-' . $this->value;
                }
                break;
            case 16:
                $x = (strlen($this->value) & 1) ? '0' . $this->value : $this->value;
                $temp = new self(Strings::hex2bin($x), 256);
                $this->value = $this->is_negative ? '-' . $temp->value : $temp->value;
                $this->is_negative = false;
                break;
            case 10:
                
                
                $this->value = $this->value === '-' ? '0' : (string)$this->value;
        }
    }

    
    public function toString()
    {
        if ($this->value === '0') {
            return '0';
        }

        return ltrim($this->value, '0');
    }

    
    public function toBytes($twos_compliment = false)
    {
        if ($twos_compliment) {
            return $this->toBytesHelper();
        }

        $value = '';
        $current = $this->value;

        if ($current[0] == '-') {
            $current = substr($current, 1);
        }

        while (bccomp($current, '0', 0) > 0) {
            $temp = bcmod($current, '16777216');
            $value = chr($temp >> 16) . chr($temp >> 8) . chr($temp) . $value;
            $current = bcdiv($current, '16777216', 0);
        }

        return $this->precision > 0 ?
            substr(str_pad($value, $this->precision >> 3, chr(0), STR_PAD_LEFT), -($this->precision >> 3)) :
            ltrim($value, chr(0));
    }

    
    public function add(BCMath $y)
    {
        $temp = new self();
        $temp->value = bcadd($this->value, $y->value);

        return $this->normalize($temp);
    }

    
    public function subtract(BCMath $y)
    {
        $temp = new self();
        $temp->value = bcsub($this->value, $y->value);

        return $this->normalize($temp);
    }

    
    public function multiply(BCMath $x)
    {
        $temp = new self();
        $temp->value = bcmul($this->value, $x->value);

        return $this->normalize($temp);
    }

    
    public function divide(BCMath $y)
    {
        $quotient = new self();
        $remainder = new self();

        $quotient->value = bcdiv($this->value, $y->value, 0);
        $remainder->value = bcmod($this->value, $y->value);

        if ($remainder->value[0] == '-') {
            $remainder->value = bcadd($remainder->value, $y->value[0] == '-' ? substr($y->value, 1) : $y->value, 0);
        }

        return [$this->normalize($quotient), $this->normalize($remainder)];
    }

    
    public function modInverse(BCMath $n)
    {
        return $this->modInverseHelper($n);
    }

    
    public function extendedGCD(BCMath $n)
    {
        
        
        

        $u = $this->value;
        $v = $n->value;

        $a = '1';
        $b = '0';
        $c = '0';
        $d = '1';

        while (bccomp($v, '0', 0) != 0) {
            $q = bcdiv($u, $v, 0);

            $temp = $u;
            $u = $v;
            $v = bcsub($temp, bcmul($v, $q, 0), 0);

            $temp = $a;
            $a = $c;
            $c = bcsub($temp, bcmul($a, $q, 0), 0);

            $temp = $b;
            $b = $d;
            $d = bcsub($temp, bcmul($b, $q, 0), 0);
        }

        return [
            'gcd' => $this->normalize(new static($u)),
            'x' => $this->normalize(new static($a)),
            'y' => $this->normalize(new static($b))
        ];
    }

    
    public function gcd(BCMath $n)
    {
        extract($this->extendedGCD($n));
        
        return $gcd;
    }

    
    public function abs()
    {
        $temp = new static();
        $temp->value = strlen($this->value) && $this->value[0] == '-' ?
            substr($this->value, 1) :
            $this->value;

        return $temp;
    }

    
    public function bitwise_and(BCMath $x)
    {
        return $this->bitwiseAndHelper($x);
    }

    
    public function bitwise_or(BCMath $x)
    {
        return $this->bitwiseXorHelper($x);
    }

    
    public function bitwise_xor(BCMath $x)
    {
        return $this->bitwiseXorHelper($x);
    }

    
    public function bitwise_rightShift($shift)
    {
        $temp = new static();
        $temp->value = bcdiv($this->value, bcpow('2', $shift, 0), 0);

        return $this->normalize($temp);
    }

    
    public function bitwise_leftShift($shift)
    {
        $temp = new static();
        $temp->value = bcmul($this->value, bcpow('2', $shift, 0), 0);

        return $this->normalize($temp);
    }

    
    public function compare(BCMath $y)
    {
        return bccomp($this->value, $y->value, 0);
    }

    
    public function equals(BCMath $x)
    {
        return $this->value == $x->value;
    }

    
    public function modPow(BCMath $e, BCMath $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    public function powMod(BCMath $e, BCMath $n)
    {
        return $this->powModOuter($e, $n);
    }

    
    protected function powModInner(BCMath $e, BCMath $n)
    {
        try {
            $class = static::$modexpEngine[static::class];
            return $class::powModHelper($this, $e, $n, static::class);
        } catch (\Exception $err) {
            return BCMath\DefaultEngine::powModHelper($this, $e, $n, static::class);
        }
    }

    
    protected function normalize(BCMath $result)
    {
        $result->precision = $this->precision;
        $result->bitmask = $this->bitmask;

        if ($result->bitmask !== false) {
            $result->value = bcmod($result->value, $result->bitmask->value);
        }

        return $result;
    }

    
    public static function randomRangePrime(BCMath $min, BCMath $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }

    
    public static function randomRange(BCMath $min, BCMath $max)
    {
        return self::randomRangeHelper($min, $max);
    }

    
    protected function make_odd()
    {
        if (!$this->isOdd()) {
            $this->value = bcadd($this->value, '1');
        }
    }

    
    protected function testSmallPrimes()
    {
        if ($this->value === '1') {
            return false;
        }
        if ($this->value === '2') {
            return true;
        }
        if ($this->value[strlen($this->value) - 1] % 2 == 0) {
            return false;
        }

        $value = $this->value;

        foreach (self::PRIMES as $prime) {
            $r = bcmod($this->value, $prime);
            if ($r == '0') {
                return $this->value == $prime;
            }
        }

        return true;
    }

    
    public static function scan1divide(BCMath $r)
    {
        $r_value = &$r->value;
        $s = 0;
        
        while ($r_value[strlen($r_value) - 1] % 2 == 0) {
            $r_value = bcdiv($r_value, '2', 0);
            ++$s;
        }

        return $s;
    }

    
    public function pow(BCMath $n)
    {
        $temp = new self();
        $temp->value = bcpow($this->value, $n->value);

        return $this->normalize($temp);
    }

    
    public static function min(BCMath ...$nums)
    {
        return self::minHelper($nums);
    }

    
    public static function max(BCMath ...$nums)
    {
        return self::maxHelper($nums);
    }

    
    public function between(BCMath $min, BCMath $max)
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }

    
    protected static function setBitmask($bits)
    {
        $temp = parent::setBitmask($bits);
        return $temp->add(static::$one[static::class]);
    }

    
    public function isOdd()
    {
        return $this->value[strlen($this->value) - 1] % 2 == 1;
    }

    
    public function testBit($x)
    {
        return bccomp(
            bcmod($this->value, bcpow('2', $x + 1, 0)),
            bcpow('2', $x, 0),
            0
        ) >= 0;
    }

    
    public function isNegative()
    {
        return strlen($this->value) && $this->value[0] == '-';
    }

    
    public function negate()
    {
        $temp = clone $this;

        if (!strlen($temp->value)) {
            return $temp;
        }

        $temp->value = $temp->value[0] == '-' ?
            substr($this->value, 1) :
            '-' . $this->value;

        return $temp;
    }
}
