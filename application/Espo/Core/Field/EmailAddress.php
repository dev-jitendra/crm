<?php


namespace Espo\Core\Field;

use RuntimeException;

use FILTER_VALIDATE_EMAIL;


class EmailAddress
{
    private string $address;
    private bool $isOptedOut = false;
    private bool $isInvalid = false;

    public function __construct(string $address)
    {
        if ($address === '') {
            throw new RuntimeException("Empty email address.");
        }

        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Not valid email address '{$address}'.");
        }

        $this->address = $address;
    }

    
    public function getAddress(): string
    {
        return $this->address;
    }

    
    public function isOptedOut(): bool
    {
        return $this->isOptedOut;
    }

    
    public function isInvalid(): bool
    {
        return $this->isInvalid;
    }

    
    public function invalid(): self
    {
        $obj = $this->clone();

        $obj->isInvalid = true;

        return $obj;
    }

    
    public function notInvalid(): self
    {
        $obj = $this->clone();

        $obj->isInvalid = false;

        return $obj;
    }

    
    public function optedOut(): self
    {
        $obj = $this->clone();

        $obj->isOptedOut = true;

        return $obj;
    }

    
    public function notOptedOut(): self
    {
        $obj = $this->clone();

        $obj->isOptedOut = false;

        return $obj;
    }

    
    public static function create(string $address): self
    {
        return new self($address);
    }

    private function clone(): self
    {
        $obj = new self($this->address);

        $obj->isInvalid = $this->isInvalid;
        $obj->isOptedOut = $this->isOptedOut;

        return $obj;
    }
}
