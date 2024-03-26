<?php



namespace phpseclib3\Math;

use phpseclib3\Exception\BadConfigurationException;
use phpseclib3\Math\BigInteger\Engines\Engine;


class BigInteger implements \JsonSerializable
{
    
    private static $mainEngine;

    
    private static $engines;

    
    private $value;

    
    private $hex;

    
    private $precision;

    
    public static function setEngine($main, array $modexps = ['DefaultEngine'])
    {
        self::$engines = [];

        $fqmain = 'phpseclib3\\Math\\BigInteger\\Engines\\' . $main;
        if (!class_exists($fqmain) || !method_exists($fqmain, 'isValidEngine')) {
            throw new \InvalidArgumentException("$main is not a valid engine");
        }
        if (!$fqmain::isValidEngine()) {
            throw new BadConfigurationException("$main is not setup correctly on this system");
        }
        
        self::$mainEngine = $fqmain;

        $found = false;
        foreach ($modexps as $modexp) {
            try {
                $fqmain::setModExpEngine($modexp);
                $found = true;
                break;
            } catch (\Exception $e) {
            }
        }

        if (!$found) {
            throw new BadConfigurationException("No valid modular exponentiation engine found for $main");
        }

        self::$engines = [$main, $modexp];
    }

    
    public static function getEngine()
    {
        self::initialize_static_variables();

        return self::$engines;
    }

    
    private static function initialize_static_variables()
    {
        if (!isset(self::$mainEngine)) {
            $engines = [
                ['GMP', ['DefaultEngine']],
                ['PHP64', ['OpenSSL']],
                ['BCMath', ['OpenSSL']],
                ['PHP32', ['OpenSSL']],
                ['PHP64', ['DefaultEngine']],
                ['PHP32', ['DefaultEngine']]
            ];

            foreach ($engines as $engine) {
                try {
                    self::setEngine($engine[0], $engine[1]);
                    return;
                } catch (\Exception $e) {
                }
            }

            throw new \UnexpectedValueException('No valid BigInteger found. This is only possible when JIT is enabled on Windows and neither the GMP or BCMath extensions are available so either disable JIT or install GMP / BCMath');
        }
    }

    
    public function __construct($x = 0, $base = 10)
    {
        self::initialize_static_variables();

        if ($x instanceof self::$mainEngine) {
            $this->value = clone $x;
        } elseif ($x instanceof BigInteger\Engines\Engine) {
            $this->value = new static("$x");
            $this->value->setPrecision($x->getPrecision());
        } else {
            $this->value = new self::$mainEngine($x, $base);
        }
    }

    
    public function toString()
    {
        return $this->value->toString();
    }

    
    public function __toString()
    {
        return (string)$this->value;
    }

    
    public function __debugInfo()
    {
        return $this->value->__debugInfo();
    }

    
    public function toBytes($twos_compliment = false)
    {
        return $this->value->toBytes($twos_compliment);
    }

    
    public function toHex($twos_compliment = false)
    {
        return $this->value->toHex($twos_compliment);
    }

    
    public function toBits($twos_compliment = false)
    {
        return $this->value->toBits($twos_compliment);
    }

    
    public function add(BigInteger $y)
    {
        return new static($this->value->add($y->value));
    }

    
    public function subtract(BigInteger $y)
    {
        return new static($this->value->subtract($y->value));
    }

    
    public function multiply(BigInteger $x)
    {
        return new static($this->value->multiply($x->value));
    }

    
    public function divide(BigInteger $y)
    {
        list($q, $r) = $this->value->divide($y->value);
        return [
            new static($q),
            new static($r)
        ];
    }

    
    public function modInverse(BigInteger $n)
    {
        return new static($this->value->modInverse($n->value));
    }

    
    public function extendedGCD(BigInteger $n)
    {
        extract($this->value->extendedGCD($n->value));
        
        return [
            'gcd' => new static($gcd),
            'x' => new static($x),
            'y' => new static($y)
        ];
    }

    
    public function gcd(BigInteger $n)
    {
        return new static($this->value->gcd($n->value));
    }

    
    public function abs()
    {
        return new static($this->value->abs());
    }

    
    public function setPrecision($bits)
    {
        $this->value->setPrecision($bits);
    }

    
    public function getPrecision()
    {
        return $this->value->getPrecision();
    }

    
    public function __sleep()
    {
        $this->hex = $this->toHex(true);
        $vars = ['hex'];
        if ($this->getPrecision() > 0) {
            $vars[] = 'precision';
        }
        return $vars;
    }

    
    public function __wakeup()
    {
        $temp = new static($this->hex, -16);
        $this->value = $temp->value;
        if ($this->precision > 0) {
            
            $this->setPrecision($this->precision);
        }
    }

    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = ['hex' => $this->toHex(true)];
        if ($this->precision > 0) {
            $result['precision'] = $this->getPrecision();
        }
        return $result;
    }

    
    public function powMod(BigInteger $e, BigInteger $n)
    {
        return new static($this->value->powMod($e->value, $n->value));
    }

    
    public function modPow(BigInteger $e, BigInteger $n)
    {
        return new static($this->value->modPow($e->value, $n->value));
    }

    
    public function compare(BigInteger $y)
    {
        return $this->value->compare($y->value);
    }

    
    public function equals(BigInteger $x)
    {
        return $this->value->equals($x->value);
    }

    
    public function bitwise_not()
    {
        return new static($this->value->bitwise_not());
    }

    
    public function bitwise_and(BigInteger $x)
    {
        return new static($this->value->bitwise_and($x->value));
    }

    
    public function bitwise_or(BigInteger $x)
    {
        return new static($this->value->bitwise_or($x->value));
    }

    
    public function bitwise_xor(BigInteger $x)
    {
        return new static($this->value->bitwise_xor($x->value));
    }

    
    public function bitwise_rightShift($shift)
    {
        return new static($this->value->bitwise_rightShift($shift));
    }

    
    public function bitwise_leftShift($shift)
    {
        return new static($this->value->bitwise_leftShift($shift));
    }

    
    public function bitwise_leftRotate($shift)
    {
        return new static($this->value->bitwise_leftRotate($shift));
    }

    
    public function bitwise_rightRotate($shift)
    {
        return new static($this->value->bitwise_rightRotate($shift));
    }

    
    public static function minMaxBits($bits)
    {
        self::initialize_static_variables();

        $class = self::$mainEngine;
        extract($class::minMaxBits($bits));
        
        return [
            'min' => new static($min),
            'max' => new static($max)
        ];
    }

    
    public function getLength()
    {
        return $this->value->getLength();
    }

    
    public function getLengthInBytes()
    {
        return $this->value->getLengthInBytes();
    }

    
    public static function random($size)
    {
        self::initialize_static_variables();

        $class = self::$mainEngine;
        return new static($class::random($size));
    }

    
    public static function randomPrime($size)
    {
        self::initialize_static_variables();

        $class = self::$mainEngine;
        return new static($class::randomPrime($size));
    }

    
    public static function randomRangePrime(BigInteger $min, BigInteger $max)
    {
        $class = self::$mainEngine;
        return new static($class::randomRangePrime($min->value, $max->value));
    }

    
    public static function randomRange(BigInteger $min, BigInteger $max)
    {
        $class = self::$mainEngine;
        return new static($class::randomRange($min->value, $max->value));
    }

    
    public function isPrime($t = false)
    {
        return $this->value->isPrime($t);
    }

    
    public function root($n = 2)
    {
        return new static($this->value->root($n));
    }

    
    public function pow(BigInteger $n)
    {
        return new static($this->value->pow($n->value));
    }

    
    public static function min(BigInteger ...$nums)
    {
        $class = self::$mainEngine;
        $nums = array_map(function ($num) {
            return $num->value;
        }, $nums);
        return new static($class::min(...$nums));
    }

    
    public static function max(BigInteger ...$nums)
    {
        $class = self::$mainEngine;
        $nums = array_map(function ($num) {
            return $num->value;
        }, $nums);
        return new static($class::max(...$nums));
    }

    
    public function between(BigInteger $min, BigInteger $max)
    {
        return $this->value->between($min->value, $max->value);
    }

    
    public function __clone()
    {
        $this->value = clone $this->value;
    }

    
    public function isOdd()
    {
        return $this->value->isOdd();
    }

    
    public function testBit($x)
    {
        return $this->value->testBit($x);
    }

    
    public function isNegative()
    {
        return $this->value->isNegative();
    }

    
    public function negate()
    {
        return new static($this->value->negate());
    }

    
    public static function scan1divide(BigInteger $r)
    {
        $class = self::$mainEngine;
        return $class::scan1divide($r->value);
    }

    
    public function createRecurringModuloFunction()
    {
        $func = $this->value->createRecurringModuloFunction();
        return function (BigInteger $x) use ($func) {
            return new static($func($x->value));
        };
    }

    
    public function bitwise_split($split)
    {
        return array_map(function ($val) {
            return new static($val);
        }, $this->value->bitwise_split($split));
    }
}
