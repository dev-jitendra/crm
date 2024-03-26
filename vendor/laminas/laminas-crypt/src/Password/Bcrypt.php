<?php

namespace Laminas\Crypt\Password;

use Laminas\Math\Rand;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function is_array;
use function mb_strlen;
use function microtime;
use function password_hash;
use function password_verify;
use function sprintf;
use function strtolower;
use function trigger_error;

use const E_USER_DEPRECATED;
use const PASSWORD_BCRYPT;
use const PHP_VERSION_ID;


class Bcrypt implements PasswordInterface
{
    public const MIN_SALT_SIZE = 22;

    
    protected $cost = '10';

    
    protected $salt;

    
    public function __construct($options = [])
    {
        if (! empty($options)) {
            if ($options instanceof Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            }

            if (! is_array($options)) {
                throw new Exception\InvalidArgumentException(
                    'The options parameter must be an array or a Traversable'
                );
            }

            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'salt':
                        $this->setSalt($value);
                        break;
                    case 'cost':
                        $this->setCost($value);
                        break;
                }
            }
        }
    }

    
    public function create($password)
    {
        $options = ['cost' => (int) $this->cost];
        if (PHP_VERSION_ID < 70000) { 
            $salt            = $this->salt ?: Rand::getBytes(self::MIN_SALT_SIZE);
            $options['salt'] = $salt;
        }
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    
    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    
    public function setCost($cost)
    {
        if (! empty($cost)) {
            $cost = (int) $cost;
            if ($cost < 4 || $cost > 31) {
                throw new Exception\InvalidArgumentException(
                    'The cost parameter of bcrypt must be in range 04-31'
                );
            }
            $this->cost = sprintf('%1$02d', $cost);
        }
        return $this;
    }

    
    public function getCost()
    {
        return $this->cost;
    }

    
    public function setSalt($salt)
    {
        if (PHP_VERSION_ID >= 70000) {
            trigger_error('Salt support is deprecated starting with PHP 7.0.0', E_USER_DEPRECATED);
        }

        if (mb_strlen($salt, '8bit') < self::MIN_SALT_SIZE) {
            throw new Exception\InvalidArgumentException(
                'The length of the salt must be at least ' . self::MIN_SALT_SIZE . ' bytes'
            );
        }

        $this->salt = $salt;
        return $this;
    }

    
    public function getSalt()
    {
        if (PHP_VERSION_ID >= 70000) {
            trigger_error('Salt support is deprecated starting with PHP 7.0.0', E_USER_DEPRECATED);
        }

        return $this->salt;
    }

    
    public function benchmarkCost($timeTarget = 0.05)
    {
        $cost = 8;

        do {
            $cost++;
            $start = microtime(true);
            password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        return $cost;
    }
}
