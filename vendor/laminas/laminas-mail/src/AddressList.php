<?php

namespace Laminas\Mail;

use Countable;
use Iterator;
use Laminas\Mail\Address\AddressInterface;
use ReturnTypeWillChange;

use function count;
use function current;
use function gettype;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function key;
use function next;
use function reset;
use function sprintf;
use function strtolower;
use function var_export;


class AddressList implements Countable, Iterator
{
    
    protected $addresses = [];

    
    public function add($emailOrAddress, $name = null)
    {
        if (is_string($emailOrAddress)) {
            $emailOrAddress = $this->createAddress($emailOrAddress, $name);
        }

        if (! $emailOrAddress instanceof AddressInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an email address or %s\Address object as its first argument; received "%s"',
                __METHOD__,
                __NAMESPACE__,
                is_object($emailOrAddress) ? $emailOrAddress::class : gettype($emailOrAddress)
            ));
        }

        $email = strtolower($emailOrAddress->getEmail());
        if ($this->has($email)) {
            return $this;
        }

        $this->addresses[$email] = $emailOrAddress;
        return $this;
    }

    
    public function addMany(array $addresses)
    {
        foreach ($addresses as $key => $value) {
            if (is_int($key) || is_numeric($key)) {
                $this->add($value);
                continue;
            }

            if (! is_string($key)) {
                throw new Exception\RuntimeException(sprintf(
                    'Invalid key type in provided addresses array ("%s")',
                    is_object($key) ? $key::class : var_export($key, true)
                ));
            }

            $this->add($key, $value);
        }
        return $this;
    }

    
    public function addFromString($address, $comment = null)
    {
        $this->add(Address::fromString($address, $comment));
        return $this;
    }

    
    public function merge(self $addressList)
    {
        foreach ($addressList as $address) {
            $this->add($address);
        }
        return $this;
    }

    
    public function has($email)
    {
        $email = strtolower($email);
        return isset($this->addresses[$email]);
    }

    
    public function get($email)
    {
        $email = strtolower($email);
        if (! isset($this->addresses[$email])) {
            return false;
        }

        return $this->addresses[$email];
    }

    
    public function delete($email)
    {
        $email = strtolower($email);
        if (! isset($this->addresses[$email])) {
            return false;
        }

        unset($this->addresses[$email]);
        return true;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->addresses);
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->addresses);
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->addresses);
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->addresses);
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        return next($this->addresses);
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        $key = key($this->addresses);
        return $key !== null && $key !== false;
    }

    
    protected function createAddress($email, $name)
    {
        return new Address($email, $name);
    }
}
