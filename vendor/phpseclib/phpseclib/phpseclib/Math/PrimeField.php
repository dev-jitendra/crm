<?php



namespace phpseclib3\Math;

use phpseclib3\Math\Common\FiniteField;
use phpseclib3\Math\PrimeField\Integer;


class PrimeField extends FiniteField
{
    
    private static $instanceCounter = 0;

    
    protected $instanceID;

    
    public function __construct(BigInteger $modulo)
    {
        if (!$modulo->isPrime()) {
            throw new \UnexpectedValueException('PrimeField requires a prime number be passed to the constructor');
        }

        $this->instanceID = self::$instanceCounter++;
        Integer::setModulo($this->instanceID, $modulo);
        Integer::setRecurringModuloFunction($this->instanceID, $modulo->createRecurringModuloFunction());
    }

    
    public function setReduction(\Closure $func)
    {
        $this->reduce = $func->bindTo($this, $this);
    }

    
    public function newInteger(BigInteger $num)
    {
        return new Integer($this->instanceID, $num);
    }

    
    public function randomInteger()
    {
        static $one;
        if (!isset($one)) {
            $one = new BigInteger(1);
        }

        return new Integer($this->instanceID, BigInteger::randomRange($one, Integer::getModulo($this->instanceID)));
    }

    
    public function getLengthInBytes()
    {
        return Integer::getModulo($this->instanceID)->getLengthInBytes();
    }

    
    public function getLength()
    {
        return Integer::getModulo($this->instanceID)->getLength();
    }

    
    public function __destruct()
    {
        Integer::cleanupCache($this->instanceID);
    }
}
