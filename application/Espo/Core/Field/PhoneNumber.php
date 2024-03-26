<?php


namespace Espo\Core\Field;

use RuntimeException;


class PhoneNumber
{
    private string $number;
    private ?string $type = null;
    private bool $isOptedOut = false;
    private bool $isInvalid = false;

    public function __construct(string $number)
    {
        if ($number === '') {
            throw new RuntimeException("Empty phone number.");
        }

        $this->number = $number;
    }

    
    public function getType(): ?string
    {
        return $this->type;
    }

    
    public function getNumber(): string
    {
        return $this->number;
    }

    
    public function isOptedOut(): bool
    {
        return $this->isOptedOut;
    }

    
    public function isInvalid(): bool
    {
        return $this->isInvalid;
    }

    
    public function withType(string $type): self
    {
        $obj = $this->clone();

        $obj->type = $type;

        return $obj;
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

    
    public static function create(string $number): self
    {
        return new self($number);
    }

    
    public static function createWithType(string $number, string $type): self
    {
        return self::create($number)->withType($type);
    }

    private function clone(): self
    {
        $obj = new self($this->number);

        $obj->type = $this->type;
        $obj->isInvalid = $this->isInvalid;
        $obj->isOptedOut = $this->isOptedOut;

        return $obj;
    }
}
